<?php

namespace App\Livewire\Admin;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;

class PendingDocuments extends Component
{
    use WithPagination;

    public $search = '';
    public $showDocument = null;

    public function toggleDocumentStatus($documentId)
    {
        $document = Document::findOrFail($documentId);
        $document->update([
            'is_public' => !$document->is_public
        ]);
        $this->dispatch('document-status-updated');
    }

    public function showDocumentDetails($documentId)
    {
        $this->showDocument = Document::with(['category', 'uploader'])->findOrFail($documentId);
    }

    public function render()
    {
        $pendingDocuments = Document::query()
            ->where('is_public', false)
            ->when($this->search, fn($query) =>
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->with(['category', 'uploader'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pending-documents', [
            'pendingDocuments' => $pendingDocuments
        ]);
    }
}
