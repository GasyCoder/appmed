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
        if ($document->isExternalLink()) {
            abort(404);
        }
        
        if (!$document->fileExists()) {
            Log::error("FILE NOT FOUND", [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
            ]);
            abort(404, 'Fichier introuvable');
        }

        $disk = Storage::disk('public');
        $path = $document->file_path;
        
        // ✅ RÉCUPÉRER LE CONTENU DIRECTEMENT
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            Log::error("PHYSICAL FILE NOT FOUND", [
                'document_id' => $document->id,
                'full_path' => $fullPath,
            ]);
            abort(404, 'Fichier physique introuvable');
        }

        $size = filesize($fullPath);
        $content = file_get_contents($fullPath);
        
        // ✅ VÉRIFIER QUE LE FICHIER N'EST PAS VIDE
        if ($size === 0 || $size === false || !$content) {
            Log::error("FILE IS EMPTY", [
                'document_id' => $document->id,
                'size' => $size,
            ]);
            abort(500, 'Le fichier est vide');
        }

        $filename = $document->getDisplayFilename();
        $ext = $document->extensionFromPath();
        
        // ✅ DÉTECTER LE BON MIME TYPE
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        Log::info("SERVING FILE", [
            'document_id' => $document->id,
            'path' => $path,
            'filename' => $filename,
            'size' => $size,
            'mime' => $mime,
            'ext' => $ext,
        ]);

        // ✅ RENVOYER LE CONTENU AVEC LES BONS HEADERS
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
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
