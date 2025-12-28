<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    // UI
    public string $viewType = 'grid';     // grid|list
    public string $scope = 'active';      // active|archives

    // commun
    public string $search = '';

    // étudiant
    public string $teacherFilter = '';
    public string $semesterFilter = '';
    public string $viewedFilter = 'all';  // all|viewed|unviewed

    // enseignant
    public string $filterNiveau = '';
    public string $filterParcour = '';
    public string $filterSemestre = '';
    public string $filterStatus = '';     // ''|0|1
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'viewType' => ['except' => 'grid'],
        'scope' => ['except' => 'active'],

        'teacherFilter' => ['except' => ''],
        'semesterFilter' => ['except' => ''],
        'viewedFilter' => ['except' => 'all'],

        'filterNiveau' => ['except' => ''],
        'filterParcour' => ['except' => ''],
        'filterSemestre' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected array $teacherSortable = ['title', 'created_at', 'view_count', 'is_actif', 'download_count'];

    public function mount(): void
    {
        $this->authorize('viewAny', Document::class);

        if ($this->isStudent()) {
            $u = Auth::user();
            $this->filterNiveau = (string) ($u->niveau_id ?? '');
            $this->filterParcour = (string) ($u->parcour_id ?? '');
        }
    }

    public function isTeacher(): bool
    {
        return Auth::user()?->hasRole('teacher') === true;
    }

    public function isStudent(): bool
    {
        return Auth::user()?->hasRole('student') === true;
    }

    public function setScope(string $scope): void
    {
        $this->scope = in_array($scope, ['active','archives'], true) ? $scope : 'active';
        $this->resetPage();
    }

    public function toggleView(string $type): void
    {
        $this->viewType = in_array($type, ['grid','list'], true) ? $type : 'grid';
    }

    public function updated($name): void
    {
        $this->resetPage();
    }

    private function baseQuery(): Builder
    {
        $user = Auth::user();
        $isArchives = ($this->scope === 'archives');

        if ($this->isTeacher()) {
            $q = Document::query()->where('uploaded_by', $user->id);

            if ($this->filterNiveau !== '') $q->where('niveau_id', $this->filterNiveau);
            if ($this->filterParcour !== '') $q->where('parcour_id', $this->filterParcour);
            if ($this->filterSemestre !== '') $q->where('semestre_id', $this->filterSemestre);
            if ($this->filterStatus !== '') $q->where('is_actif', (int) $this->filterStatus);

            if ($isArchives) $q->where('is_archive', 1);
            else $q->where(fn($qq) => $qq->whereNull('is_archive')->orWhere('is_archive', 0));

            return $q;
        }

        // étudiant
        $q = Document::query()
            ->where('is_actif', true)
            ->where('niveau_id', $user->niveau_id);

        if (!empty($user->parcour_id)) {
            $q->where('parcour_id', $user->parcour_id);
        }

        if ($isArchives) $q->where('is_archive', 1);
        else $q->where(fn($qq) => $qq->whereNull('is_archive')->orWhere('is_archive', 0));

        return $q;
    }

    public function sortBy(string $field): void
    {
        if (!$this->isTeacher()) return;

        if (!in_array($field, $this->teacherSortable, true)) $field = 'created_at';

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            return;
        }

        $this->sortField = $field;
        $this->sortDirection = in_array($field, ['created_at','view_count','download_count'], true) ? 'desc' : 'asc';
    }

    // ===== teacher actions (Policy protected) =====

    public function toggleStatus(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);
        $this->authorize('toggleStatus', $doc);

        $doc->forceFill(['is_actif' => ! (bool) $doc->is_actif])->save();
        $this->dispatch('toast', type: 'success', message: 'Statut mis à jour');
    }

    public function toggleArchive(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);
        $this->authorize('toggleArchive', $doc);

        $doc->update(['is_archive' => ! (bool) $doc->is_archive]);
        $this->dispatch('toast', type: 'success', message: $doc->is_archive ? 'Document archivé' : 'Document restauré');
    }

    public function deleteDocument(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);
        $this->authorize('delete', $doc);

        $filePath = $doc->file_path;
        $doc->delete();

        if ($filePath && !Document::where('file_path', $filePath)->exists()) {
            Storage::disk('public')->delete($filePath);
        }

        $this->dispatch('toast', type: 'success', message: 'Document supprimé');
    }

    // ===== student actions (Policy protected) =====

    public function markViewed(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);
        $this->authorize('view', $doc);

        $doc->registerView(Auth::user());
        $this->dispatch('$refresh');
    }

    public function markDownload(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);
        $this->authorize('download', $doc);

        $doc->registerDownload(Auth::user());
        $this->dispatch('$refresh');
    }

    // ===== computed lists =====

    public function getTeachersProperty()
    {
        if (!$this->isStudent()) return collect();

        $studentNiveauId = Auth::user()->niveau_id;
        $isArchives = ($this->scope === 'archives');

        return User::query()
            ->role('teacher')
            ->whereHas('teacherNiveaux', fn($q) => $q->where('niveau_id', $studentNiveauId))
            ->whereHas('documents', function ($q) use ($isArchives) {
                $q->where('is_actif', true);
                if ($isArchives) $q->where('is_archive', 1);
                else $q->where(fn($qq) => $qq->whereNull('is_archive')->orWhere('is_archive', 0));
            })
            ->get();
    }

    public function render()
    {
        $user = Auth::user();

        $q = $this->baseQuery()
            ->when($this->search !== '', fn($qq) => $qq->where('title', 'like', "%{$this->search}%"))
            ->when($this->isStudent() && $this->semesterFilter !== '', fn($qq) => $qq->where('semestre_id', $this->semesterFilter))
            ->when($this->isStudent() && $this->teacherFilter !== '', fn($qq) => $qq->where('uploaded_by', $this->teacherFilter))
            ->when($this->isStudent() && $this->viewedFilter === 'viewed', fn($qq) => $qq->whereHas('views', fn($v) => $v->where('user_id', $user->id)))
            ->when($this->isStudent() && $this->viewedFilter === 'unviewed', fn($qq) => $qq->whereDoesntHave('views', fn($v) => $v->where('user_id', $user->id)))
            ->with(['uploader', 'niveau', 'parcour', 'semestre'])
            ->latest();

        if ($this->isTeacher()) {
            $field = in_array($this->sortField, $this->teacherSortable, true) ? $this->sortField : 'created_at';
            $dir = $this->sortDirection === 'asc' ? 'asc' : 'desc';
            $q->orderBy($field, $dir);
        }

        $documents = $q->paginate($this->isTeacher() ? 10 : 12);

        $viewedCount = 0;
        $unviewedCount = 0;

        if ($this->isStudent()) {
            $base = $this->baseQuery();
            $viewedCount = (clone $base)->whereHas('views', fn($v) => $v->where('user_id', $user->id))->count();
            $unviewedCount = (clone $base)->whereDoesntHave('views', fn($v) => $v->where('user_id', $user->id))->count();
        }

        // Totaux scope
        $activeTotal = ($this->isTeacher()
            ? Document::where('uploaded_by', $user->id)
            : Document::where('is_actif', true)->where('niveau_id', $user->niveau_id)
        )->where(fn($qq) => $qq->whereNull('is_archive')->orWhere('is_archive', 0))->count();

        $archivedTotal = ($this->isTeacher()
            ? Document::where('uploaded_by', $user->id)
            : Document::where('is_actif', true)->where('niveau_id', $user->niveau_id)
        )->where('is_archive', 1)->count();

        return view('livewire.documents.document-index', [
            'documents' => $documents,
            'isTeacher' => $this->isTeacher(),
            'isStudent' => $this->isStudent(),
            'teachers' => $this->teachers,
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'parcours' => Parcour::where('status', true)->orderBy('name')->get(),
            'semestres' => Semestre::where('status', true)->orderBy('name')->get(),
            'viewedCount' => $viewedCount,
            'unviewedCount' => $unviewedCount,
            'activeTotal' => $activeTotal,
            'archivedTotal' => $archivedTotal,
        ]);
    }
}
