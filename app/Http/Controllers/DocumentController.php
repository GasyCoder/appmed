<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * ✅ MÉTHODE PRINCIPALE : Affiche le viewer pour un document
     * Appelée par la route /documents/{document}/viewer
     */
    public function viewer(Document $document)
    {
        $user = Auth::user();

        // Note: La vérification d'accès est déjà faite par le middleware
        // Mais on peut la garder pour sécurité supplémentaire
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
     * Affiche la vue du viewer PDF
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

        return view('documents.viewer', [
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
        ]);
    }

    /**
     * ✅ Sert le fichier PDF (pour iframe ou ouverture directe)
     * Appelée par la route /documents/serve/{document}
     */
    public function serve(Document $document, Request $request)
    {
        $user = Auth::user();

        // Note: Vérification déjà faite par middleware, mais on garde par sécurité
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
     * ✅ Téléchargement direct (fichiers locaux ou liens externes)
     * Appelée par la route /documents/download/{document}
     */
    public function download(Document $document)
    {
        $user = Auth::user();

        // Note: Vérification déjà faite par middleware
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

    /**
     * ✅ NOUVELLE MÉTHODE : Ouvrir un document externe en lecture
     * Appelée par la route /documents/{document}/open-external
     * 
     * Redirige vers l'URL de lecture externe (Google Docs/Drive preview)
     */
    public function openExternal(Document $document)
    {
        $user = Auth::user();

        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // Enregistrer la vue
        $document->registerView($user);

        // Vérifier que c'est bien un lien externe
        if (!$document->isExternalLink()) {
            abort(400, 'Ce document n\'est pas un lien externe.');
        }

        // Récupérer l'URL de lecture
        $readUrl = $document->externalReadUrl();

        // Rediriger vers l'URL externe
        return redirect()->away($readUrl);
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Télécharger un document externe
     * Appelée par la route /documents/{document}/download-external
     * 
     * Redirige vers l'URL de téléchargement externe (Google Docs/Drive export)
     */
    public function downloadExternal(Document $document)
    {
        $user = Auth::user();

        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // Enregistrer le téléchargement
        $document->registerDownload($user);

        // Vérifier que c'est bien un lien externe
        if (!$document->isExternalLink()) {
            abort(400, 'Ce document n\'est pas un lien externe.');
        }

        // Récupérer l'URL de téléchargement
        $downloadUrl = $document->externalDownloadUrl();

        // Rediriger vers l'URL de téléchargement
        return redirect()->away($downloadUrl);
    }

    /**
     * ✅ OPTIONNEL : Accès public à un document (avec signature)
     * Appelée par la route /documents/public/{document}
     * 
     * Permet de partager un document via un lien signé temporaire
     */
    public function public(Document $document)
    {
        // Pas de vérification d'accès : le lien signé fait office d'autorisation

        // Enregistrer la vue (anonyme)
        $document->increment('view_count');

        // Si PDF → viewer
        if ($document->isPdfLocal()) {
            return view('documents.viewer', [
                'document' => $document,
                'teacherInfo' => null,
                'ext' => 'pdf',
                'isPdf' => true,
                'fileUrl' => route('document.serve', [
                    'document' => $document->id,
                    'embedded' => 1
                ]),
                'pdfFullUrl' => route('document.serve', [
                    'document' => $document->id
                ]),
                'downloadRoute' => route('document.download', $document),
            ]);
        }

        // Sinon téléchargement direct
        if ($document->isExternalLink()) {
            return redirect()->away($document->file_path);
        }

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