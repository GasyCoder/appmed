<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use Livewire\Component;
use App\Models\Document;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use Livewire\Component;
use App\Models\Document;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Documents extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $filterNiveau = '';
    public $filterParcour = '';
    public $filterSemestre = '';
    public $filterStatus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterNiveau' => ['except' => ''],
        'filterParcour' => ['except' => ''],
        'filterSemestre' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // Définir les listeners pour les événements
    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterNiveau()
    {
        $this->resetPage();
        if ($this->filterNiveau === '') {
            $this->filterSemestre = '';
        }
    }

    public function updatedFilterParcour()
    {
        $this->resetPage();
    }

    public function updatedFilterSemestre()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus($documentId)
    {
        $document = Document::find($documentId);
        if ($document && $document->uploaded_by === auth()->id()) {
            $document->update(['is_actif' => !$document->is_actif]);
            $this->dispatch('document-updated');
        }
    }

    public function deleteDocument($documentId)
    {
        $document = Document::findOrFail($documentId);

        if ($document->uploaded_by !== auth()->id()) {
            return;
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        session()->flash('success', 'Document supprimé avec succès');
    }

    public function getSemestres()
    {
        if ($this->filterNiveau) {
            return Semestre::where('niveau_id', $this->filterNiveau)
                          ->where('status', true)
                          ->orderBy('name')
                          ->get();
        }
        return Semestre::where('status', true)->orderBy('name')->get();
    }

    public function render()
    {
        $documents = Document::query()
            ->where('uploaded_by', auth()->id())
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterNiveau, function ($query) {
                return $query->where('niveau_id', $this->filterNiveau);
            })
            ->when($this->filterParcour, function ($query) {
                return $query->where('parcour_id', $this->filterParcour);
            })
            ->when($this->filterSemestre, function ($query) {
                return $query->where('semestre_id', $this->filterSemestre);
            })
            ->when($this->filterStatus !== '', function ($query) {
                return $query->where('is_actif', $this->filterStatus);
            })
            ->with(['niveau', 'parcour', 'semestre'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.teacher.documents', [
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'parcours' => Parcour::where('status', true)->orderBy('name')->get(),
            'semestres' => $this->getSemestres(),
            'myDocuments' => $documents,
            'uploadCount' => Document::where('uploaded_by', auth()->id())->count(),
            'totalDownloads' => Document::where('uploaded_by', auth()->id())->sum('download_count'),
        ]);
    }
}
