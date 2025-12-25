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

    public $tries = 2; // 2 tentatives
    public $timeout = 180; // 3 minutes max

    public function __construct(
        public Document $document,
        public string $tempFilePath
    ) {}

    public function handle(PdfConversionService $pdfService): void
    {
        try {
            Log::info("Début conversion PDF", [
                'document_id' => $this->document->id,
                'temp_file' => $this->tempFilePath,
            ]);

            // 1. Vérifier que le fichier temporaire existe
            if (!file_exists($this->tempFilePath)) {
                throw new \RuntimeException("Fichier temporaire introuvable");
            }

            // 2. Dossier de sortie
            $outputDir = storage_path('app/public/documents');
            if (!is_dir($outputDir)) {
                @mkdir($outputDir, 0755, true);
            }

            // 3. Nom du PDF final
            $slug = Str::slug($this->document->title) ?: 'document';
            $timestamp = time();
            $random = Str::random(6);
            $pdfFileName = "{$timestamp}_{$slug}_{$random}.pdf";

            // 4. Convertir
            $pdfPath = $pdfService->convertToPdf($this->tempFilePath, $outputDir, $pdfFileName);

            if (!$pdfPath || !file_exists($pdfPath)) {
                throw new \RuntimeException("Échec de génération du PDF");
            }

            $fileSize = filesize($pdfPath) ?: 0;
            $relativePath = 'documents/' . $pdfFileName;

            // 5. Supprimer l'ancien fichier temporaire du storage
            if ($this->document->file_path && Storage::disk('public')->exists($this->document->file_path)) {
                Storage::disk('public')->delete($this->document->file_path);
            }

            // 6. Mettre à jour le document
            $this->document->update([
                'file_path' => $relativePath,
                'protected_path' => $relativePath,
                'file_size' => $fileSize,
                'file_type' => 'pdf',
                'converted_from' => $this->document->original_extension,
                'converted_at' => now(),
            ]);

            // 7. Nettoyer le fichier temporaire
            @unlink($this->tempFilePath);

            Log::info("Conversion PDF réussie", [
                'document_id' => $this->document->id,
                'pdf_path' => $pdfPath,
                'size' => $fileSize,
            ]);

        } catch (\Exception $e) {
            // Nettoyer en cas d'erreur
            @unlink($this->tempFilePath);

            Log::error("Échec conversion PDF", [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Ne pas relancer le job si échec
            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job conversion PDF échoué définitivement", [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);

        // Nettoyer
        @unlink($this->tempFilePath);
    }
}