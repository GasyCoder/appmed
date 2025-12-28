<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function viewer(Document $document)
    {
        if (!$document->isViewerLocalType()) abort(404);

        // ✅ Compter UNE fois ici
        $document->registerView();
        $document->refresh();

        $ext = $document->extensionFromPath();
        $isPdf = ($ext === 'pdf');

        // ✅ embedded=1 => serve() ne recompte pas
        $fileUrl = route('document.serve', ['document' => $document->id, 'embedded' => 1]);
        $downloadRoute = route('document.download', $document);

        $onlineViewerUrl = null;
        if (in_array($ext, ['ppt', 'pptx'], true)) {
            $publicUrl = URL::temporarySignedRoute(
                'document.public',
                now()->addMinutes(30),
                ['document' => $document->id, 'embedded' => 1]
            );

            $onlineViewerUrl = 'https://docs.google.com/gview?embedded=1&url=' . urlencode($publicUrl);
        }

        $teacher = $document->uploader;
        $teacherInfo = $teacher ? [
            'name'  => $teacher->name ?? '',
            'grade' => $teacher->profil->grade ?? null,
        ] : null;

        return view('documents.viewer', compact(
            'document', 'ext', 'isPdf', 'fileUrl', 'onlineViewerUrl', 'downloadRoute', 'teacherInfo'
        ));
    }

    public function serve(Request $request, Document $document)
    {
        if ($document->isExternalLink()) abort(404);
        if (!$document->fileExists()) abort(404, 'Fichier introuvable');

        // ✅ Ne pas recompter si vient du viewer (iframe/gview)
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

    public function openExternal(Document $document)
    {
        if (!$document->isExternalLink()) abort(404);

        $document->registerView();
        $document->refresh();

        return redirect()->away($document->externalReadUrl());
    }

    public function downloadExternal(Document $document)
    {
        if (!$document->isExternalLink()) abort(404);

        // ✅ si pas convertible, on ouvre en lecture
        if (method_exists($document, 'canExternalDownload') && !$document->canExternalDownload()) {
            return redirect()->route('document.openExternal', $document);
        }

        // fallback ancien comportement
        if (!method_exists($document, 'canExternalDownload') && !$document->isDirectDownloadType()) {
            return redirect()->route('document.openExternal', $document);
        }

        $document->registerDownload();
        $document->refresh();

        return redirect()->away($document->externalDownloadUrl());
    }
}
