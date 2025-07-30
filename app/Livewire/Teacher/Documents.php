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
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Documents extends Component
{
    use WithFileUploads;
    use WithPagination;
    use LivewireAlert;

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

    // CORRIGÉ : Ajouter les listeners pour les événements de rafraîchissement
    protected $listeners = [
        'refresh' => '$refresh',
        'document-updated' => 'handleDocumentUpdated',
        'document-created' => 'handleDocumentCreated',
        'document-deleted' => 'handleDocumentDeleted'
    ];

    public function mount()
    {
        $this->resetPage();
    }

    // NOUVEAU : Gérer les événements de mise à jour
    public function handleDocumentUpdated()
    {
        $this->resetPage();
        $this->alert('success', 'Document mis à jour avec succès', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
        
        // Forcer le rafraîchissement
        $this->dispatch('$refresh');
    }

    public function handleDocumentCreated()
    {
        $this->resetPage();
        $this->alert('success', 'Document créé avec succès', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function handleDocumentDeleted()
    {
        $this->resetPage();
        $this->alert('success', 'Document supprimé avec succès', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
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
        
        // Forcer le rechargement après tri
        $this->resetPage();
    }

    public function toggleStatus($documentId)
    {
        try {
            $document = Document::find($documentId);
            
            if (!$document || $document->uploaded_by !== auth()->id()) {
                $this->alert('error', 'Document non trouvé ou accès non autorisé', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }

            $newStatus = !$document->is_actif;
            $document->update(['is_actif' => $newStatus]);
            
            $statusText = $newStatus ? 'partagé' : 'non partagé';
            
            $this->alert('success', "Document maintenant {$statusText}", [
                'position' => 'top-end',
                'timer' => 2000,
                'toast' => true,
            ]);
            
            // Émettre l'événement de mise à jour
            $this->dispatch('document-updated');
            
            Log::info("Document status toggled", [
                'document_id' => $documentId,
                'new_status' => $newStatus,
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error toggling document status', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $this->alert('error', 'Erreur lors de la mise à jour du statut', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    public function deleteDocument($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);

            if ($document->uploaded_by !== auth()->id()) {
                $this->alert('error', 'Accès non autorisé', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }

            // CORRIGÉ : Supprimer tous les documents avec le même file_path (pour gérer la logique niveau/semestre)
            $documentsToDelete = Document::where('file_path', $document->file_path)
                ->where('uploaded_by', auth()->id())
                ->get();

            $deletedCount = 0;
            $filePath = $document->file_path;

            foreach ($documentsToDelete as $doc) {
                $doc->delete();
                $deletedCount++;
            }

            // Supprimer le fichier physique une seule fois
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info("Physical file deleted: {$filePath}");
            }

            Log::info("Documents deleted", [
                'file_path' => $filePath,
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id()
            ]);

            $this->alert('success', "{$deletedCount} entrée(s) supprimée(s) avec succès", [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            // Émettre l'événement de suppression
            $this->dispatch('document-deleted');
            
        } catch (\Exception $e) {
            Log::error('Error deleting document', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $this->alert('error', 'Erreur lors de la suppression: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);
        }
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

    // NOUVEAU : Méthode pour rafraîchir manuellement
    public function refreshDocuments()
    {
        $this->resetPage();
        $this->dispatch('$refresh');
        
        $this->alert('info', 'Liste rafraîchie', [
            'position' => 'top-end',
            'timer' => 2000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        // CORRIGÉ : Améliorer la requête avec rechargement forcé des relations
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
            // CORRIGÉ : Recharger systématiquement les relations
            ->with(['niveau:id,name', 'parcour:id,name', 'semestre:id,name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // CORRIGÉ : Ajouter la propriété formatted_size à chaque document
        $documents->getCollection()->transform(function ($document) {
            $document->formatted_size = $this->formatFileSize($document->file_size);
            return $document;
        });

        return view('livewire.teacher.documents', [
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'parcours' => Parcour::where('status', true)->orderBy('name')->get(),
            'semestres' => $this->getSemestres(),
            'myDocuments' => $documents,
            'uploadCount' => Document::where('uploaded_by', auth()->id())->count(),
            'totalDownloads' => Document::where('uploaded_by', auth()->id())->sum('download_count'),
        ]);
    }

    // NOUVEAU : Méthode pour formater la taille des fichiers
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' octets';
        }
    }
}