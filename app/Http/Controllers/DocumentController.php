<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function serve(Document $document)
    {
        // Sécurité minimale (le middleware document.access fera le vrai contrôle si tu l’as)
        abort_unless(Auth::check(), 403);

        $filePath = (string) ($document->file_path ?? '');

        // ✅ Si c'est un lien externe => redirection
        if (Str::startsWith($filePath, ['http://', 'https://'])) {
            return redirect()->away($filePath);
        }

        abort_if($filePath === '', 404);
        abort_unless(Storage::disk('public')->exists($filePath), 404);

        $absolutePath = Storage::disk('public')->path($filePath);

        // ✅ Toujours résoudre le mime depuis le fichier (plus fiable que DB)
        $mime = Storage::disk('public')->mimeType($filePath) ?: 'application/octet-stream';

        $downloadName = $this->buildDownloadName($document, $filePath);

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Document $document)
    {
        abort_unless(Auth::check(), 403);

        $filePath = (string) ($document->file_path ?? '');

        // ✅ Si externe : soit tu désactives, soit tu redirects (ici: redirect)
        if (Str::startsWith($filePath, ['http://', 'https://'])) {
            return redirect()->away($filePath);
        }

        abort_if($filePath === '', 404);
        abort_unless(Storage::disk('public')->exists($filePath), 404);

        $absolutePath = Storage::disk('public')->path($filePath);

        $mime = Storage::disk('public')->mimeType($filePath) ?: 'application/octet-stream';
        $downloadName = $this->buildDownloadName($document, $filePath);

        // ✅ Force un vrai nom de fichier .pdf/.docx/etc (plus de .html)
        return response()->download($absolutePath, $downloadName, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function buildDownloadName(Document $document, string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) ?: 'bin';

        $title = trim((string) ($document->title ?? 'document'));
        $slug = Str::slug($title);
        if ($slug === '') $slug = 'document';

        return $slug.'.'.$ext;
    }
}
