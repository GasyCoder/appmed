<?php
// app/Jobs/ConvertDocumentToPdf.php

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

    public int $tries = 2;
    public int $timeout = 180;

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

        // ✅ Si LO absent => on skip proprement, pas d'échec queue
        if (!$pdfService->isLibreOfficeAvailable()) {
            $this->document->update([
                'conversion_status' => 'skipped',
                'conversion_error'  => "Conversion PDF indisponible sur ce serveur (LibreOffice absent).",
            ]);

            @unlink($this->tempFilePath);

            Log::warning("Conversion ignorée (LibreOffice absent)", [
                'document_id' => $this->document->id,
            ]);

            return;
        }

        try {
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

            // supprimer l'ancien (original office) du public si existe
            if ($this->document->file_path && Storage::disk('public')->exists($this->document->file_path)) {
                Storage::disk('public')->delete($this->document->file_path);
            }

            $this->document->update([
                'file_path'          => $relativePath,
                'protected_path'     => $relativePath,
                'file_size'          => $fileSize,
                'file_type'          => 'pdf',
                'converted_from'     => $this->document->original_extension,
                'converted_at'       => now(),
                'conversion_status'  => 'done',
                'conversion_error'   => null,
            ]);

            @unlink($this->tempFilePath);

            Log::info("Conversion PDF réussie", [
                'document_id' => $this->document->id,
                'pdf_path' => $pdfPath,
                'size' => $fileSize,
            ]);
        } catch (\Throwable $e) {
            @unlink($this->tempFilePath);

            $this->document->update([
                'conversion_status' => 'failed',
                'conversion_error'  => $e->getMessage(),
            ]);

            Log::error("Échec conversion PDF", [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);

            // ✅ on relance pour les vraies erreurs (tries=2)
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->document->update([
            'conversion_status' => 'failed',
            'conversion_error'  => $exception->getMessage(),
        ]);

        @unlink($this->tempFilePath);

        Log::error("Job conversion PDF échoué définitivement", [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
