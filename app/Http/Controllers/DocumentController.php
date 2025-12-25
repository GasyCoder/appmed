<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Viewer: UNIQUEMENT pour fichiers LOCAUX pdf/pptx
     */
    public function viewer(Document $document)
    {
        if (!$document->isViewerLocalType()) {
            abort(404);
        }

        Log::info("VIEWER APPELE (document {$document->id})");

        // ✅ compteur vue
        $document->registerView();
        $document->refresh();

        $ext = $document->extensionFromPath();
        $isPdf = ($ext === 'pdf');

        // ✅ download local => route (compteur)
        $downloadRoute = route('document.download', $document);

        // PDF local : pdf.js va lire via serve route (inline)
        $fileUrl = route('document.serve', $document);

        // PPTX local : on affiche un viewer Google (gview) en iframe
        // => besoin d’une URL publique signée qui renvoie le fichier en inline
        $onlineViewerUrl = null;
        if (in_array($ext, ['ppt', 'pptx'], true)) {
            $publicUrl = URL::temporarySignedRoute(
                'document.public',
                now()->addMinutes(30),
                ['document' => $document->id]
            );
            $onlineViewerUrl = 'https://docs.google.com/gview?embedded=1&url=' . urlencode($publicUrl);
        }

        $teacher = $document->uploader;
        $teacherInfo = $teacher ? [
            'name'  => $teacher->name ?? '',
            'grade' => $teacher->profil->grade ?? null,
        ] : null;

        return view('documents.viewer', [
            'document' => $document,
            'ext' => $ext,
            'isPdf' => $isPdf,
            'fileUrl' => $fileUrl,                 // pdf.js
            'onlineViewerUrl' => $onlineViewerUrl, // pptx local
            'downloadRoute' => $downloadRoute,
            'teacherInfo' => $teacherInfo,
        ]);
    }

    /**
     * Serve: UNIQUEMENT local (pour pdf.js et public signed)
     */
    public function serve(Document $document)
    {
        if ($document->isExternalLink()) abort(404);
        if (!$document->fileExists()) abort(404, 'Fichier introuvable');

        $disk = Storage::disk('public');
        $path = $document->file_path;

        $filename = $document->getDisplayFilename();
        $mime = $document->file_type ?: $disk->mimeType($path) ?: 'application/octet-stream';

        // inline => lecture
        return $disk->response($path, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
        ]);
    }

    /**
     * Public signed: doit renvoyer le fichier local en inline (pour gview pptx)
     */
    public function public(Document $document)
    {
        // route est déjà "signed" dans web.php
        return $this->serve($document);
    }

    /**
     * Download: UNIQUEMENT local
     * - docx/xls/csv local => direct download
     * - pdf/pptx local => download possible aussi
     */
    public function download(Document $document)
    {
        if ($document->isExternalLink()) abort(404);
        if (!$document->fileExists()) abort(404, 'Fichier introuvable');

        Log::info("DOWNLOAD LOCAL (document {$document->id})");

        $document->registerDownload();
        $document->refresh();

        return Storage::disk('public')->download(
            $document->file_path,
            $document->getDisplayFilename()
        );
    }

    /**
     * EXTERNE: ouverture lecture (nouvel onglet)
     * - incrémente view_count
     */
    public function openExternal(Document $document)
    {
        if (!$document->isExternalLink()) abort(404);

        Log::info("OPEN EXTERNAL (document {$document->id})");

        $document->registerView();
        $document->refresh();

        return redirect()->away($document->externalReadUrl());
    }

    /**
     * EXTERNE: téléchargement direct (docx/xls/csv)
     * - incrémente download_count
     */
    public function downloadExternal(Document $document)
    {
        if (!$document->isExternalLink()) abort(404);

        // Règle: docx/xls/csv => download obligatoire
        if (!$document->isDirectDownloadType()) {
            // si appelé par erreur, on renvoie vers lecture
            return redirect()->route('document.openExternal', $document);
        }

        Log::info("DOWNLOAD EXTERNAL (document {$document->id})");

        $document->registerDownload();
        $document->refresh();

        return redirect()->away($document->externalDownloadUrl());
    }
}
