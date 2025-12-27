<?php

namespace App\Livewire\Teacher;

use App\Models\Document;
use App\Models\DocumentView;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Documents extends Component
{
    use WithPagination, LivewireAlert;

    // Filtres
    public string $search = '';
    public string $filterNiveau = '';
    public string $filterParcour = '';
    public string $filterSemestre = '';
    public string $filterStatus = '';
    public string $filterArchive = '0'; // 0 actifs, 1 archives, '' tous

    // Tri
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterNiveau' => ['except' => ''],
        'filterParcour' => ['except' => ''],
        'filterSemestre' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterArchive' => ['except' => '0'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected array $sortable = [
        'title',
        'is_actif',
        'created_at',
        'view_count',
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, [
            'search','filterNiveau','filterParcour','filterSemestre','filterStatus','filterArchive'
        ], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, $this->sortable, true)) {
            $field = 'created_at';
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            return;
        }

        $this->sortField = $field;
        $this->sortDirection = in_array($field, ['created_at', 'view_count'], true) ? 'desc' : 'asc';
    }

    public function setArchiveFilter(string $value): void
    {
        $this->filterArchive = $value;
        $this->resetPage();
    }
    

    public function toggleArchive(int $documentId): void
    {
        try {
            $document = Document::where('uploaded_by', Auth::id())->findOrFail($documentId);

            $document->update(['is_archive' => !$document->is_archive]);

            $this->alert('success', $document->is_archive ? 'Document archivé' : 'Document restauré', [
                'position' => 'top-end',
                'timer' => 2000,
                'toast' => true,
            ]);
        } catch (\Throwable $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => "Impossible de modifier l'archive",
            ]);
        }
    }


    public function toggleStatus(int $documentId): void
    {
        try {
            $document = Document::query()
                ->whereKey($documentId)
                ->where('uploaded_by', Auth::id())
                ->firstOrFail();

            $document->forceFill(['is_actif' => ! (bool) $document->is_actif])->save();

            $this->alert('success', 'Statut mis à jour', [
                'position' => 'top-end',
                'timer' => 2000,
                'toast' => true,
                'text' => $document->is_actif ? 'Document partagé' : 'Document non partagé',
            ]);
        } catch (\Throwable $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Impossible de modifier le statut',
            ]);
        }
    }

    public function deleteDocument(int $documentId): void
    {
        try {
            $document = Document::query()
                ->whereKey($documentId)
                ->where('uploaded_by', Auth::id())
                ->firstOrFail();

            $filePath = $document->file_path;
            $document->delete();

            if ($filePath && !Document::where('file_path', $filePath)->exists()) {
                Storage::disk('public')->delete($filePath);
            }

            $this->alert('success', 'Document supprimé', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Throwable $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Impossible de supprimer le document',
            ]);
        }
    }

    public function render()
    {
        $baseQuery = Document::query()
            ->where('uploaded_by', Auth::id());

        $query = (clone $baseQuery)
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->filterNiveau !== '', fn ($q) => $q->where('niveau_id', $this->filterNiveau))
            ->when($this->filterParcour !== '', fn ($q) => $q->where('parcour_id', $this->filterParcour))
            ->when($this->filterSemestre !== '', fn ($q) => $q->where('semestre_id', $this->filterSemestre))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('is_actif', (int) $this->filterStatus))
            ->when($this->filterArchive !== '', fn ($q) => $q->where('is_archive', (int) $this->filterArchive))
            ->with(['niveau', 'parcour', 'semestre'])
            ->withCount('views')
            ->orderBy(
                in_array($this->sortField, $this->sortable, true) ? $this->sortField : 'created_at',
                $this->sortDirection === 'asc' ? 'asc' : 'desc'
            );

        $documents = $query->paginate(10);

        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'shared'    => (clone $baseQuery)->where('is_actif', true)->count(),
            'notShared' => (clone $baseQuery)->where('is_actif', false)->count(),
            'recent'    => (clone $baseQuery)->where('created_at', '>=', now()->subDays(7))->count(),
            'views'     => (clone $baseQuery)->sum('view_count'),
            'archived'  => (clone $baseQuery)->where('is_archive', true)->count(),
            'uniqueViews' => DocumentView::query()
                ->whereHas('document', fn ($q) => $q->where('uploaded_by', Auth::id()))
                ->count(),
        ];

        return view('livewire.teacher.documents', [
            'documents' => $documents,
            'stats' => $stats,
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'parcours' => Parcour::where('status', true)->orderBy('name')->get(),
            'semestres' => $this->filterNiveau !== ''
                ? Semestre::where('niveau_id', $this->filterNiveau)->where('status', true)->orderBy('name')->get()
                : Semestre::where('status', true)->orderBy('name')->get(),
        ]);
    }
}
