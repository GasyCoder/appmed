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

    public $tries = 1; // Une seule tentative (si LibreOffice manque, ça ne sert à rien de réessayer)
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

        // Vérification préalable
        if (!file_exists($this->tempFilePath)) {
            throw new \RuntimeException("Fichier temporaire introuvable: {$this->tempFilePath}");
        }

        // Double vérification de LibreOffice
        if (!$pdfService->isLibreOfficeAvailable()) {
            Log::warning("LibreOffice non disponible - abandon conversion", [
                'document_id' => $this->document->id,
            ]);
            
            // Ne pas lancer d'exception, juste abandonner proprement
            $this->cleanupTempFile();
            return;
        }

        try {
            $outputDir = storage_path('app/public/documents');
            if (!is_dir($outputDir)) {
                @mkdir($outputDir, 0755, true);
            }

            $slug = Str::slug($this->document->title) ?: 'document';
            $timestamp = time();
            $random = Str::random(6);
            $pdfFileName = "{$timestamp}_{$slug}_{$random}.pdf";

            // Conversion
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

            // Mettre à jour le document
            $this->document->update([
                'file_path' => $relativePath,
                'protected_path' => $relativePath,
                'file_size' => $fileSize,
                'file_type' => 'pdf',
                'converted_from' => $this->document->original_extension,
                'converted_at' => now(),
            ]);

            $this->cleanupTempFile();

            Log::info("Conversion PDF réussie", [
                'document_id' => $this->document->id,
                'pdf_path' => $pdfPath,
                'size' => $fileSize,
            ]);

        } catch (\Throwable $e) {
            Log::error("Erreur lors de la conversion PDF", [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->cleanupTempFile();
            
            // Re-throw pour que le job soit marqué comme échoué
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job conversion PDF échoué définitivement", [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);

        $this->cleanupTempFile();

        // Optionnel : marquer le document comme "échec de conversion"
        // Vous pourriez ajouter un champ 'conversion_failed' dans la table
        try {
            $this->document->update([
                'converted_from' => null,
                'converted_at' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error("Impossible de mettre à jour le document après échec", [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function cleanupTempFile(): void
    {
        if (file_exists($this->tempFilePath)) {
            @unlink($this->tempFilePath);
        }
    }
}