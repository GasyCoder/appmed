<?php
// app/Http/Controllers/DocumentController.php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function viewer(Document $document)
    {
        if (!$document->isViewerLocalType()) abort(404);

        $document->registerView();
        $document->refresh();

        $fileUrl = route('document.serve', ['document' => $document->id, 'embedded' => 1]);
        $downloadRoute = route('document.download', $document);

        return view('documents.viewer', compact('document', 'fileUrl', 'downloadRoute'));
    }

    public function serve(Request $request, Document $document)
    {
        if ($document->isExternalLink()) abort(404);
        if (!$document->fileExists()) abort(404, 'Fichier introuvable');

        if (!$request->boolean('embedded')) {
            $document->registerView();
            $document->refresh();
        }

        $filename = $document->getDisplayFilename();

        return Storage::disk('public')->response($document->file_path, $filename, [
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function public(Request $request, Document $document)
    {
        return $this->serve($request, $document);
    }

    public function download(Document $document)
    {
        if ($document->isExternalLink()) abort(404);
        if (!$document->fileExists()) abort(404, 'Fichier introuvable');

        $document->registerDownload();
        $document->refresh();

        return Storage::disk('public')->download(
            $document->file_path,
            $document->getDisplayFilename()
        );
    }
}
