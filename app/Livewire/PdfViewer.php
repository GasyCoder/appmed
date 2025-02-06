<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;

class PdfViewer extends Component
{
    public $showModal = false;
    public $document = null;
    public $documentData = [];

    #[On('view-document')]
    public function viewDocument($documentId)
    {
        try {
            $document = Document::with('teacher')->findOrFail($documentId);

            if (!$document->canAccess(auth()->user())) {
                throw new \Exception('Accès non autorisé');
            }

            $filePath = $document->file_path;

            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('Fichier non trouvé');
            }

            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $url = Storage::url($filePath);

            $this->document = $document;
            $this->documentData = [
                'url' => $url,
                'id' => $document->id,
                'title' => $document->title,
                'teacherName' => $document->teacher->name ?? "Non assigné",
                'originalExtension' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION))
            ];

            $document->incrementViewCount();
            $this->showModal = true;

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Impossible d'ouvrir le document: " . $e->getMessage()
            ]);
        }
    }

    public function incrementView()
    {
        if ($this->document) {
            $this->document->registerView();
            $this->dispatch('viewsUpdated', [
                'documentId' => $this->document->id,
                'viewCount' => $this->document->view_count
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->document = null;
        $this->documentData = [];
    }

    public function render()
    {
        return view('livewire.document.pdf-viewer');
    }
}
