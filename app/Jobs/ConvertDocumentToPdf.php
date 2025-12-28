<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\PdfConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertDocumentToPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 180;

    public function __construct(
        public Document $document,
        public string $tempFilePath
    ) {}

    public function handle(PdfConversionService $pdfService): void
    {
        Log::info("Début conversion PDF", [
            'document_id' => $this->document->id,
            'temp_file' => $this->tempFilePath,
        ]);

        if (!file_exists($this->tempFilePath)) {
            throw new \RuntimeException("Fichier temporaire introuvable");
        }

        $outputDir = storage_path('app/public/documents');
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        $slug = Str::slug($this->document->title) ?: 'document';
        $timestamp = time();
        $random = Str::random(6);
        $pdfFileName = "{$timestamp}_{$slug}_{$random}.pdf";

        $pdfPath = $pdfService->convertToPdf($this->tempFilePath, $outputDir, $pdfFileName);

        if (!$pdfPath || !file_exists($pdfPath)) {
            throw new \RuntimeException("Échec de génération du PDF");
        }

        $fileSize = filesize($pdfPath) ?: 0;
        $relativePath = 'documents/' . $pdfFileName;

        // Supprimer l'ancien fichier (original) du storage public
        if ($this->document->file_path && Storage::disk('public')->exists($this->document->file_path)) {
            Storage::disk('public')->delete($this->document->file_path);
        }

        $this->document->update([
            'file_path' => $relativePath,
            'protected_path' => $relativePath,
            'file_size' => $fileSize,
            'file_type' => 'pdf',
            'converted_from' => $this->document->original_extension,
            'converted_at' => now(),
        ]);

        @unlink($this->tempFilePath);

        Log::info("Conversion PDF réussie", [
            'document_id' => $this->document->id,
            'pdf_path' => $pdfPath,
            'size' => $fileSize,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job conversion PDF échoué définitivement", [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);

        @unlink($this->tempFilePath);
    }
}
