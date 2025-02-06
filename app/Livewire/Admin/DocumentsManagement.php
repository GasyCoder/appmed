<?php

namespace App\Livewire\Admin;

use App\Models\Document;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentsManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $selectedDocument = null;

    public function toggleDocumentAccess($documentId)
    {
        $document = Document::findOrFail($documentId);
        $document->update([
            'is_public' => !$document->is_public
        ]);

        $this->dispatch('document-access-updated');
    }

    public function render()
    {
        $documents = Document::query()
            ->when($this->search, fn($query) =>
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->when($this->categoryFilter, fn($query) =>
                $query->where('category_id', $this->categoryFilter)
            )
            ->with(['category', 'uploader'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.documents-management', [
            'documents' => $documents,
            'categories' => Category::all()
        ]);
    }
}
