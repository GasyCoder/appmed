<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Affiche un document (viewer pour PDF, téléchargement pour le reste)
     */
    public function show(Document $document)
    {
        $user = Auth::user();

        // Vérifier accès
        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        // Enregistrer la vue (si étudiant)
        $document->registerView($user);

        $ext = $document->extensionFromPath();

        // ✅ CAS 1 : FICHIERS OFFICE (doc, docx, ppt, pptx, xls, xlsx) → TÉLÉCHARGEMENT DIRECT
        if ($document->isDirectDownloadType()) {
            return $this->download($document);
        }

        // ✅ CAS 2 : PDF LOCAL → VIEWER
        if ($document->isPdfLocal()) {
            return $this->showPdfViewer($document);
        }

        // ✅ CAS 3 : LIEN GOOGLE (Drive/Docs) → REDIRECTION
        if ($document->isExternalLink() && $document->isGoogleLink()) {
            $readUrl = $document->externalReadUrl();
            return redirect()->away($readUrl);
        }

        // ✅ CAS 4 : AUTRE LIEN EXTERNE → REDIRECTION
        if ($document->isExternalLink()) {
            return redirect()->away($document->file_path);
        }

        // ✅ CAS 5 : AUTRE FICHIER LOCAL → TÉLÉCHARGEMENT
        return $this->download($document);
    }

    /**
     * Affiche le viewer PDF
     */
    private function showPdfViewer(Document $document)
    {
        $teacherInfo = null;
        if ($document->teacher) {
            $teacherInfo = [
                'name' => $document->teacher->name ?? '',
                'grade' => $document->teacher->grade ?? '',
            ];
        }

        return view('documents.show', [
            'document' => $document,
            'teacherInfo' => $teacherInfo,
            'ext' => 'pdf',
            'isPdf' => true,
            
            // URL pour l'iframe (embedded=1)
            'fileUrl' => route('document.serve', [
                'document' => $document->id,
                'embedded' => 1
            ]),
            
            // URL plein écran (ouvre le PDF natif du navigateur)
            'pdfFullUrl' => route('document.serve', [
                'document' => $document->id
            ]),
            
            'downloadRoute' => route('document.download', $document),
            'onlineViewerUrl' => null,
        ]);
    }

    /**
     * Sert le fichier PDF (pour iframe ou ouverture directe)
     */
    public function serve(Document $document, Request $request)
    {
        $user = Auth::user();

        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        if (!$document->fileExists()) {
            abort(404, 'Fichier introuvable.');
        }

        // Seulement pour PDF
        if ($document->extensionFromPath() !== 'pdf') {
            abort(400, 'Ce endpoint est réservé aux PDF.');
        }

        $path = Storage::disk('public')->path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'Fichier physique introuvable.');
        }

        $embedded = (int) $request->get('embedded', 0);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $embedded === 1 
                ? 'inline; filename="' . $document->getDisplayFilename() . '"'
                : 'attachment; filename="' . $document->getDisplayFilename() . '"',
        ]);
    }

    /**
     * Téléchargement direct (pour tous types sauf PDF en mode viewer)
     */
    public function download(Document $document)
    {
        $user = Auth::user();

        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // Enregistrer le téléchargement (si étudiant)
        $document->registerDownload($user);

        // ✅ Lien externe Google
        if ($document->isExternalLink() && $document->isGoogleLink()) {
            $downloadUrl = $document->externalDownloadUrl();
            return redirect()->away($downloadUrl);
        }

        // ✅ Autre lien externe
        if ($document->isExternalLink()) {
            return redirect()->away($document->file_path);
        }

        // ✅ Fichier local
        if (!$document->fileExists()) {
            abort(404, 'Fichier introuvable.');
        }

        $path = Storage::disk('public')->path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'Fichier physique introuvable.');
        }

        return response()->download($path, $document->getDisplayFilename());
    }
}