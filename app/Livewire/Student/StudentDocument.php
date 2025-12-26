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
       'viewedFilter' => ['except' => 'all'], // ✅ AJOUT
   ];

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

       return User::query()
           ->role('teacher')
           ->whereHas('teacherNiveaux', function($query) use ($studentNiveauId) {
               $query->where('niveau_id', $studentNiveauId);
           })
           ->whereHas('documents', function($query) {
               $query->where('is_actif', true);
           })
           ->with(['teacherNiveaux' => function($query) use ($studentNiveauId) {
               $query->where('niveau_id', $studentNiveauId);
           }])
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

       $documents = Document::query()
           ->where('is_actif', true)
           ->when($this->filterNiveau, function($query) {
               $query->where('niveau_id', $this->filterNiveau);
           })
           ->when($this->filterParcour, function($query) {
               $query->where('parcour_id', $this->filterParcour);
           })
           ->when($this->semesterFilter, function($query) {
               $query->where('semestre_id', $this->semesterFilter);
           })
           ->when($this->teacherFilter, function($query) {
               $query->where('uploaded_by', $this->teacherFilter);
           })
           ->when($this->search, function($query) {
               $query->where('title', 'like', "%{$this->search}%");
           })
           // ✅ NOUVEAU : Filtre vu/non vu
           ->when($this->viewedFilter === 'viewed', function($query) use ($user) {
               $query->whereHas('views', function($q) use ($user) {
                   $q->where('user_id', $user->id);
               });
           })
           ->when($this->viewedFilter === 'unviewed', function($query) use ($user) {
               $query->whereDoesntHave('views', function($q) use ($user) {
                   $q->where('user_id', $user->id);
               });
           })
           ->with([
               'uploader.teacherNiveaux',
               'niveau',
               'parcour',
               'semestre'
           ])
           ->latest()
           ->paginate(12);

       // ✅ NOUVEAU : Compteurs pour les badges
       $viewedCount = Document::where('is_actif', true)
           ->where('niveau_id', $user->niveau_id)
           ->where('parcour_id', $user->parcour_id)
           ->whereHas('views', fn($q) => $q->where('user_id', $user->id))
           ->count();

       $unviewedCount = Document::where('is_actif', true)
           ->where('niveau_id', $user->niveau_id)
           ->where('parcour_id', $user->parcour_id)
           ->whereDoesntHave('views', fn($q) => $q->where('user_id', $user->id))
           ->count();

       return view('livewire.student.student-document', [
           'documents' => $documents,
           'teachers' => $this->teachers,
           'niveaux' => $this->niveaux,
           'parcours' => $this->parcours,
           'semestres' => $this->semestres,
           'viewedCount' => $viewedCount,
           'unviewedCount' => $unviewedCount,
           'recentDocuments' => Document::query()
               ->where('is_actif', true)
               ->where('niveau_id', $user->niveau_id)
               ->where('parcour_id', $user->parcour_id)
               ->latest()
               ->take(5)
               ->get()
       ]);
   }
}