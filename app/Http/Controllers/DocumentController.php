<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentPdfService $pdfService) {}

    public function viewer(Document $document)
    {
        $user = Auth::user();

        if (!$user || !$document->canAccess($user)) {
            abort(403, 'Accès non autorisé à ce document.');
        }

        // 1) EXTERNE -> pas de viewer interne, ouvre lecteur externe
        if ($document->isExternalLink()) {
            $document->registerView($user); // optionnel, et chez vous ça ne compte que student
            return redirect()->away($document->externalReadUrl() ?? $document->externalUrl());
        }

        // 2) LOCAL -> viewer (PDF natif ou PDF converti)
        $document->registerView($user);

        $pdfRel = $this->pdfService->getPdfRelativePath($document);

        // si non convertible -> fallback download
        if (!$pdfRel) {
            return $this->download($document);
        }

        $teacherInfo = null;
        if ($document->teacher) {
            $teacherInfo = [
                'name' => $document->teacher->name ?? '',
                'grade' => $document->teacher->grade ?? '',
            ];
        }

        return view('livewire.documents.viewer', [
            'document' => $document,
            'teacherInfo' => $teacherInfo,
            'ext' => 'pdf',
            'isPdf' => true,
            'fileUrl' => route('document.serve', ['document' => $document->id, 'embedded' => 1]),
            'pdfFullUrl' => route('document.serve', ['document' => $document->id]),
            'downloadRoute' => route('document.download', $document),
        ]);
    }

    public function serve(Document $document, Request $request)
    {
        $user = Auth::user();

        if (!$user || !$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // Pas de serve pour externe
        if ($document->isExternalLink()) {
            return redirect()->route('document.openExternal', $document);
        }

        $pdfRel = $this->pdfService->getPdfRelativePath($document);
        if (!$pdfRel || !Storage::disk('public')->exists($pdfRel)) {
            return redirect()->route('document.viewer', $document);
        }

        $abs = Storage::disk('public')->path($pdfRel);

        // Toujours inline (viewer/plein écran). Le download passe par document.download
        return response()->file($abs, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->getDisplayFilename('pdf') . '"',
        ]);
    }

    public function download(Document $document)
    {
        $user = Auth::user();

        if (!$user || !$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // EXTERNE: pas de compteur download
        if ($document->isExternalLink()) {
            $url = $document->externalDownloadUrl() ?: $document->externalUrl();
            return redirect()->away($url);
        }

        // LOCAL: compteur download
        $document->registerDownload($user);

        if (!$document->fileExists()) {
            abort(404, 'Fichier introuvable.');
        }

        $abs = Storage::disk('public')->path($document->file_path);

        return response()->download(
            $abs,
            $document->getDisplayFilename($document->extensionFromPath())
        );
    }

    public function openExternal(Document $document)
    {
        $user = Auth::user();

        if (!$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        // On peut compter la vue (ok)
        $document->registerView($user);

        if (!$document->isExternalLink()) {
            abort(400, "Ce document n'est pas un lien externe.");
        }

        // ✅ LECTURE (preview), pas download
        return redirect()->away($document->externalReadUrl());
    }


    public function downloadExternal(Document $document)
    {
        $user = Auth::user();

        if (!$user || !$document->canAccess($user)) {
            abort(403, 'Accès non autorisé.');
        }

        if (!$document->isExternalLink()) {
            return redirect()->route('document.download', $document);
        }

        // SANS compteur
        return redirect()->away($document->externalDownloadUrl() ?: $document->externalUrl());
    }

    public function public(Document $document)
    {
        // optionnel, selon votre logique : ici je laisse simple
        $document->increment('view_count');

        if ($document->isExternalLink()) {
            return redirect()->away($document->externalReadUrl() ?? $document->externalUrl());
        }

        $pdfRel = $this->pdfService->getPdfRelativePath($document);
        if ($pdfRel && Storage::disk('public')->exists($pdfRel)) {
            $abs = Storage::disk('public')->path($pdfRel);
            return response()->file($abs, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->getDisplayFilename('pdf') . '"',
            ]);
        }

        if (!$document->fileExists()) abort(404);

        $abs = Storage::disk('public')->path($document->file_path);
        return response()->download($abs, $document->getDisplayFilename($document->extensionFromPath()));
    }
}
