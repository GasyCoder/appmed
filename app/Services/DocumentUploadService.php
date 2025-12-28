<?php

namespace App\Services;

use App\Data\UploadDocumentRequest;
use App\Jobs\ConvertDocumentToPdf;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadService
{
    public function __construct(
        private PdfConversionService $pdfService
    ) {}

    public function handle(UploadDocumentRequest $req): int
    {
        return DB::transaction(function () use ($req) {
            $count = 0;

            // Vérifier une seule fois si LibreOffice est disponible
            $canConvert = $this->pdfService->isLibreOfficeAvailable();

            if (!$canConvert) {
                Log::warning('LibreOffice non disponible - les fichiers Office seront stockés tels quels');
            }

            // 1) Local files
            foreach ($req->files as $i => $file) {
                $title   = $this->pickTitle($req, $i, $file?->getClientOriginalName());
                $isActif = $this->pickStatus($req, $i);

                $stored = $this->storeLocalFile($file, $title, $canConvert);

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

                    'converted_from'     => null,
                    'converted_at'       => null,

                    'is_actif'       => $isActif,
                    'view_count'     => 0,
                    'download_count' => 0,
                ]);

                // Si conversion demandée ET possible → dispatch job
                if (($stored['conversion_pending'] ?? false) === true && $canConvert) {
                    $this->scheduleConversion($document, $stored);
                }

                $count++;
            }

            // 2) External links (pas de conversion)
            foreach ($req->urls as $j => $url) {
                $idx = $j;
                $title   = $this->pickTitle($req, $idx, $url);
                $isActif = $this->pickStatus($req, $idx);

                $url = (string) $url;
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

    private function scheduleConversion(Document $document, array $stored): void
    {
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $tempFileName = "convert_{$document->id}_" . time() . ".{$stored['original_extension']}";
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $tempFileName;

        $sourcePath = storage_path('app/public/' . $stored['file_path']);

        if (@copy($sourcePath, $tempPath)) {
            ConvertDocumentToPdf::dispatch($document, $tempPath);

            Log::info("Conversion PDF planifiée", [
                'document_id' => $document->id,
                'temp_path' => $tempPath,
            ]);
        } else {
            Log::error("Impossible de copier le fichier pour conversion", [
                'document_id' => $document->id,
                'sourcePath' => $sourcePath,
            ]);
        }
    }

    private function pickTitle(UploadDocumentRequest $req, int $index, ?string $fallback = null): string
    {
        $t = $req->titles[$index] ?? null;
        $t = is_string($t) ? trim($t) : '';
        if ($t !== '') return $t;

        if ($fallback) {
            $fallback = trim($fallback);
            if (str_starts_with($fallback, 'http')) return 'Lien ' . ($index + 1);
            return pathinfo($fallback, PATHINFO_FILENAME) ?: ('Document ' . ($index + 1));
        }

        return 'Document ' . ($index + 1);
    }

    private function pickStatus(UploadDocumentRequest $req, int $index): bool
    {
        return (bool) ($req->statuses[$index] ?? true);
    }

    private function storeLocalFile($file, string $title, bool $canConvert): array
    {
        $originalName = $file->getClientOriginalName();
        $originalExt  = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $originalExt  = $originalExt ?: 'bin';

        $slug = Str::slug($title) ?: 'document';
        $timestamp = time();
        $random = Str::random(6);

        $stored = $this->storeOriginalFile($file, $slug, $timestamp, $random, $originalName, $originalExt);

        // Ne marquer pour conversion QUE si LibreOffice est disponible
        $isConvertibleFormat = in_array($originalExt, ['doc', 'docx', 'ppt', 'pptx'], true);
        
        if ($isConvertibleFormat && $canConvert) {
            $stored['conversion_pending'] = true;
        } elseif ($isConvertibleFormat && !$canConvert) {
            Log::info("Fichier Office stocké sans conversion (LibreOffice indisponible)", [
                'filename' => $originalName,
                'extension' => $originalExt,
            ]);
        }

        return $stored;
    }

    private function storeOriginalFile($file, string $slug, int $timestamp, string $random, string $originalName, string $originalExt): array
    {
        $fileName = "{$timestamp}_{$slug}_{$random}.{$originalExt}";
        $path = Storage::disk('public')->putFileAs('documents', $file, $fileName);

        $size = 0;
        try {
            $size = (int) ($file->getSize() ?: 0);
        } catch (\Throwable $e) {
            Log::warning('Unable to read uploaded file size in DocumentUploadService', [
                'path' => $path,
                'msg' => $e->getMessage(),
            ]);
        }

        return [
            'file_path'          => $path,
            'file_size'          => $size,
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