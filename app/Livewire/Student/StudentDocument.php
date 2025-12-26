<?php

namespace App\Livewire\Student;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Parcour;
use Livewire\Component;
use App\Models\Document;
use App\Models\Semestre;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Auth;

class StudentDocument extends Component
{
   use WithPagination;

   public $search = '';
   public $teacherFilter = '';
   public $filterNiveau = '';
   public $filterParcour = '';
   public $filterSemestre = '';
   public $semesterFilter = '';
   public $viewType = 'grid';
   public string $scope = 'active'; // 'active' | 'archives'
   
   // ✅ NOUVEAU : Filtre vu/non vu
   public $viewedFilter = 'all'; // 'all', 'viewed', 'unviewed'

   public bool $isStudent = false;

    protected $queryString = [
    'search' => ['except' => ''],
    'teacherFilter' => ['except' => ''],
    'filterNiveau' => ['except' => ''],
    'filterParcour' => ['except' => ''],
    'filterSemestre' => ['except' => ''],
    'semesterFilter' => ['except' => ''],
    'viewType' => ['except' => 'grid'],
    'viewedFilter' => ['except' => 'all'],
    'scope' => ['except' => 'active'], // ✅ AJOUT
    ];

    public function setScope(string $scope): void
    {
        $this->scope = in_array($scope, ['active','archives'], true) ? $scope : 'active';
        $this->resetPage();
    }

   protected $listeners = [
       'refreshDocuments' => '$refresh',
       'documentViewed' => '$refresh',
   ];

   public function toggleView($type)
   {
       $this->viewType = $type;
       $this->dispatch('viewToggled', $type);
   }

   // ✅ NOUVEAU : Changer le filtre vu/non vu
   public function setViewedFilter($filter)
   {
       $this->viewedFilter = $filter;
       $this->resetPage();
   }

   public function mount()
   {
       if (!Auth::user()->hasRole('student')) {
           return redirect()->route('login');
       }

       $this->isStudent = true;
       $this->filterNiveau = Auth::user()->niveau_id;
       $this->filterParcour = Auth::user()->parcour_id;
   }

   private function documentsBaseQuery($user, ?bool $archived = null)
    {
        $q = Document::query()
            ->where('is_actif', true)
            ->where('niveau_id', $user->niveau_id);

        if (!empty($user->parcour_id)) {
            $q->where('parcour_id', $user->parcour_id);
        }

        // ✅ archive filter robuste (null = non archivé)
        if ($archived === true) {
            $q->where('is_archive', 1);
        } elseif ($archived === false) {
            $q->where(function ($qq) {
                $qq->whereNull('is_archive')->orWhere('is_archive', 0);
            });
        }

        return $q;
    }


   public function updatedFilterNiveau($value)
   {
       $this->filterSemestre = '';
       $this->semesterFilter = '';
       $this->resetPage();
   }

   public function updatedFilterParcour()
   {
       $this->resetPage();
   }

   public function updatedFilterSemestre()
   {
       $this->resetPage();
   }

   public function updatedSemesterFilter()
   {
       $this->resetPage();
   }

   public function updatedSearch()
   {
       $this->resetPage();
   }

   public function updatedTeacherFilter()
   {
       $this->resetPage();
   }

   public function refreshCounters()
   {
       $this->render();
   }

    public function getTeachersProperty()
    {
        $studentNiveauId = Auth::user()->niveau_id;
        $isArchives = ($this->scope === 'archives');

        return User::query()
            ->role('teacher')
            ->whereHas('teacherNiveaux', fn($q) => $q->where('niveau_id', $studentNiveauId))
            ->whereHas('documents', function ($q) use ($isArchives) {
                $q->where('is_actif', true);

                if ($isArchives) {
                    $q->where('is_archive', 1);
                } else {
                    $q->where(fn($qq) => $qq->whereNull('is_archive')->orWhere('is_archive', 0));
                }
            })
            ->get();
    }


   public function getNiveauxProperty()
   {
       return Niveau::where('status', true)
                   ->orderBy('name')
                   ->get();
   }

   public function getParcoursProperty()
   {
       return Parcour::where('status', true)
                     ->orderBy('name')
                     ->get();
   }

   public function getSemestresProperty()
   {
       $user = Auth::user();
       return Semestre::where('niveau_id', $user->niveau_id)
                    ->where('status', true)
                    ->orderBy('name')
                    ->get();
   }

   public function downloadDocument($id)
   {
       $document = Document::findOrFail($id);
       
       if (!$document->canAccess(Auth::user())) {
           session()->flash('error', 'Accès non autorisé');
           return;
       }

       $document->registerDownload(Auth::user());
       
       return response()->download(storage_path('app/public/' . $document->file_path));
   }

    public function render()
    {
        $user = Auth::user();
        $isArchives = ($this->scope === 'archives');

        // Base scope (active ou archives)
        $scopedBase = $this->documentsBaseQuery($user, $isArchives);

        // Liste documents (avec tes filtres)
        $documentsQuery = (clone $scopedBase)
            ->when($this->semesterFilter, fn($q) => $q->where('semestre_id', $this->semesterFilter))
            ->when($this->teacherFilter, fn($q) => $q->where('uploaded_by', $this->teacherFilter))
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->viewedFilter === 'viewed', function ($q) use ($user) {
                $q->whereHas('views', fn($vv) => $vv->where('user_id', $user->id));
            })
            ->when($this->viewedFilter === 'unviewed', function ($q) use ($user) {
                $q->whereDoesntHave('views', fn($vv) => $vv->where('user_id', $user->id));
            })
            ->with(['uploader.teacherNiveaux', 'niveau', 'parcour', 'semestre'])
            ->latest();

        $documents = $documentsQuery->paginate(12);

        // ✅ Compteurs badges (doivent respecter le scope)
        $viewedCount = (clone $scopedBase)
            ->whereHas('views', fn($q) => $q->where('user_id', $user->id))
            ->count();

        $unviewedCount = (clone $scopedBase)
            ->whereDoesntHave('views', fn($q) => $q->where('user_id', $user->id))
            ->count();

        // ✅ Totaux pour switch Actifs/Archives
        $activeTotal = $this->documentsBaseQuery($user, false)->count();
        $archivedTotal = $this->documentsBaseQuery($user, true)->count();

        return view('livewire.student.student-document', [
            'documents' => $documents,
            'teachers' => $this->teachers, // voir point 2 ci-dessous
            'niveaux' => $this->niveaux,
            'parcours' => $this->parcours,
            'semestres' => $this->semestres,
            'viewedCount' => $viewedCount,
            'unviewedCount' => $unviewedCount,
            'activeTotal' => $activeTotal,
            'archivedTotal' => $archivedTotal,
        ]);
    }

}