<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;
use App\Jobs\ConvertDocumentToPdf;
use Illuminate\Support\Facades\DB;
use App\Data\UploadDocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentUploadService
{
    protected PdfConversionService $pdfService;

    public function __construct(PdfConversionService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function handle(UploadDocumentRequest $req): int
    {
        return DB::transaction(function () use ($req) {
            $count = 0;

            // 1) Local files
            foreach ($req->files as $i => $file) {
                $title   = $this->pickTitle($req, $i, $file?->getClientOriginalName());
                $isActif = $this->pickStatus($req, $i);

                $stored = $this->storeLocalFile($file, $title);

                $document = Document::create([
                    'uploaded_by'  => $req->uploadedBy,
                    'niveau_id'    => $req->niveauId,
                    'parcour_id'   => $req->parcourId,
                    'semestre_id'  => $req->semestreId,
                    'programme_id' => $req->programmeId,

                    'title'              => $title,
                    'file_path'          => $stored['file_path'],
                    'protected_path'     => $stored['file_path'],
                    'file_size'          => $stored['file_size'],
                    'file_type'          => $stored['file_type'],

                    'original_filename'  => $stored['original_filename'] ?? null,
                    'original_extension' => $stored['original_extension'] ?? null,

                    'converted_from'     => null, // Sera rempli après conversion
                    'converted_at'       => null,

                    'is_actif'       => $isActif,
                    'view_count'     => 0,
                    'download_count' => 0,
                ]);

                // ✅ Si conversion nécessaire, lancer le Job
                if ($stored['conversion_pending'] ?? false) {
                    $tempDir = storage_path('app/temp');
                    if (!is_dir($tempDir)) {
                        @mkdir($tempDir, 0755, true);
                    }
                    
                    $tempFileName = "convert_{$document->id}_" . time() . ".{$stored['original_extension']}";
                    $tempPath = $tempDir . DIRECTORY_SEPARATOR . $tempFileName;
                    
                    $sourcePath = storage_path('app/public/' . $stored['file_path']);
                    
                    if (copy($sourcePath, $tempPath)) {
                        \App\Jobs\ConvertDocumentToPdf::dispatch($document, $tempPath);
                        
                        Log::info("Conversion PDF planifiée", [
                            'document_id' => $document->id,
                            'temp_path' => $tempPath,
                        ]);
                    } else {
                        Log::error("Impossible de copier le fichier pour conversion", [
                            'document_id' => $document->id,
                        ]);
                    }
                }

                $count++;
            }

            // 2) External links (PAS de conversion)
            foreach ($req->urls as $j => $url) {
                $idx = $j;
                $title   = $this->pickTitle($req, $idx, $url);
                $isActif = $this->pickStatus($req, $idx);

                if (mb_strlen($url) > 250) {
                    $url = mb_substr($url, 0, 250);
                }

                Document::create([
                    'uploaded_by'  => $req->uploadedBy,
                    'niveau_id'    => $req->niveauId,
                    'parcour_id'   => $req->parcourId,
                    'semestre_id'  => $req->semestreId,
                    'programme_id' => $req->programmeId,

                    'title'              => $title,
                    'file_path'          => $url,
                    'protected_path'     => null,
                    'file_size'          => 0,
                    'file_type'          => 'link',

                    'original_filename'  => $title,
                    'original_extension' => 'url',

                    'is_actif'       => $isActif,
                    'view_count'     => 0,
                    'download_count' => 0,
                ]);

                $count++;
            }

            return $count;
        });
    }

    

    private function pickTitle(UploadDocumentRequest $req, int $index, ?string $fallback = null): string
    {
        $t = $req->titles[$index] ?? null;
        $t = is_string($t) ? trim($t) : '';
        if ($t !== '') return $t;

        if ($fallback) {
            $fallback = trim($fallback);
            if (str_starts_with($fallback, 'http')) {
                return 'Lien ' . ($index + 1);
            }
            return pathinfo($fallback, PATHINFO_FILENAME) ?: ('Document ' . ($index + 1));
        }

        return 'Document ' . ($index + 1);
    }

    private function pickStatus(UploadDocumentRequest $req, int $index): bool
    {
        return (bool) ($req->statuses[$index] ?? true);
    }

    /**
     * ✅ NOUVELLE VERSION avec conversion automatique DOCX/PPTX → PDF
     */
    private function storeLocalFile($file, string $title): array
    {
        $originalName = $file->getClientOriginalName();
        $originalExt = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $originalExt = $originalExt ?: 'bin';

        $slug = Str::slug($title) ?: 'document';
        $timestamp = time();
        $random = Str::random(6);

        // ✅ Toujours stocker l'original d'abord (upload rapide)
        $result = $this->storeOriginalFile($file, $slug, $timestamp, $random, $originalName, $originalExt);

        // ✅ Si conversion nécessaire, planifier en arrière-plan
        $needsConversion = in_array($originalExt, ['doc', 'docx', 'ppt', 'pptx'], true);
        
        if ($needsConversion) {
            // Marquer que la conversion sera faite
            $result['conversion_pending'] = true;
        }

        return $result;
    }

    /**
     * Stocker et convertir en PDF
     */
    private function storeAndConvertToPdf($file, string $slug, int $timestamp, string $random, string $originalName, string $originalExt): array
    {
        // 1. Créer dossier temporaire
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        // 2. Sauvegarder temporairement le fichier original
        $tempFileName = "{$timestamp}_{$slug}_{$random}.{$originalExt}";
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $tempFileName;
        
        if (!@copy($file->getRealPath(), $tempPath)) {
            throw new \RuntimeException("Impossible de copier le fichier temporaire");
        }

        Log::info("Fichier temporaire créé pour conversion", [
            'temp_path' => $tempPath,
            'original_name' => $originalName,
        ]);

        // 3. Définir le dossier de sortie
        $outputDir = storage_path('app/public/documents');
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        // 4. Convertir en PDF
        $pdfFileName = "{$timestamp}_{$slug}_{$random}.pdf";
        
        try {
            $pdfPath = $this->pdfService->convertToPdf($tempPath, $outputDir, $pdfFileName);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                throw new \RuntimeException("Le fichier PDF n'a pas été généré");
            }

            $fileSize = filesize($pdfPath) ?: 0;
            $relativePath = 'documents/' . $pdfFileName;

            Log::info("Conversion PDF réussie", [
                'original' => $originalName,
                'pdf_path' => $pdfPath,
                'size' => $fileSize,
            ]);

            // 5. Nettoyer le fichier temporaire
            @unlink($tempPath);

            // 6. Retourner les infos
            return [
                'file_path'          => $relativePath,
                'file_size'          => $fileSize,
                'file_type'          => 'pdf',
                'original_filename'  => $originalName,
                'original_extension' => $originalExt,
                'converted_from'     => $originalExt,
                'converted_at'       => now(),
            ];

        } catch (\Exception $e) {
            // Nettoyer en cas d'erreur
            @unlink($tempPath);
            throw $e;
        }
    }

    /**
     * Stocker le fichier original sans conversion
     */
    private function storeOriginalFile($file, string $slug, int $timestamp, string $random, string $originalName, string $originalExt): array
    {
        $fileName = "{$timestamp}_{$slug}_{$random}.{$originalExt}";
        $path = Storage::disk('public')->putFileAs('documents', $file, $fileName);

        return [
            'file_path'          => $path,
            'file_size'          => (int) ($file->getSize() ?: 0),
            'file_type'          => $this->resolveFileType($originalExt, $file->getMimeType()),
            'original_filename'  => $originalName,
            'original_extension' => $originalExt,
            'converted_from'     => null,
            'converted_at'       => null,
        ];
    }

    private function resolveFileType(?string $extension, ?string $mime = null): string
    {
        $ext = strtolower(trim((string) $extension));

        if ($ext === '') {
            if ($mime && str_starts_with($mime, 'image/')) return 'image';
            if ($mime === 'application/pdf') return 'pdf';
            return 'other';
        }

        return match ($ext) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'word',
            'ppt', 'pptx' => 'powerpoint',
            'xls', 'xlsx' => 'excel',
            'jpg', 'jpeg', 'png', 'webp', 'gif' => 'image',
            default => 'other',
        };
    }
}