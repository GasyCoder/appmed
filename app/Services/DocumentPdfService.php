<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DocumentPdfService
{
    private const CONVERTIBLE = ['doc','docx','ppt','pptx','xls','xlsx'];

    public function getPdfRelativePath(Document $document): ?string
    {
        if ($document->isExternalLink()) return null;

        if ($document->isPdfLocal()) {
            return $document->file_path;
        }

        $ext = $document->extensionFromPath();
        if (!in_array($ext, self::CONVERTIBLE, true)) {
            return null;
        }

        // Cache stable : documents-cache/{id}.pdf
        $cacheRel = "documents-cache/{$document->id}.pdf";

        if (Storage::disk('public')->exists($cacheRel)) {
            return $cacheRel;
        }

        return $this->convertToPdfCache($document, $cacheRel) ? $cacheRel : null;
    }

    private function convertToPdfCache(Document $document, string $cacheRel): bool
    {
        try {
            if (!$document->fileExists()) return false;

            $bin = env('LIBREOFFICE_BIN', 'soffice');
            if (!$bin) return false;

            $inputAbs = Storage::disk('public')->path($document->file_path);

            Storage::disk('public')->makeDirectory('documents-cache');
            $outDirAbs = Storage::disk('public')->path('documents-cache');

            $process = new Process([
                $bin,
                '--headless',
                '--nologo',
                '--nofirststartwizard',
                '--norestore',
                '--convert-to', 'pdf',
                '--outdir', $outDirAbs,
                $inputAbs,
            ]);

            $process->setTimeout((int) env('LIBREOFFICE_TIMEOUT', 120));
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('LibreOffice convert failed', [
                    'doc_id' => $document->id,
                    'err' => $process->getErrorOutput(),
                    'out' => $process->getOutput(),
                ]);
                return false;
            }

            // LibreOffice génère : documents-cache/{basename}.pdf
            $base = pathinfo($inputAbs, PATHINFO_FILENAME);
            $generatedAbs = rtrim($outDirAbs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $base . '.pdf';

            if (!file_exists($generatedAbs)) {
                Log::error('LibreOffice output missing', ['doc_id' => $document->id, 'expected' => $generatedAbs]);
                return false;
            }

            // Renommer vers cache stable {id}.pdf
            $targetAbs = Storage::disk('public')->path($cacheRel);
            @rename($generatedAbs, $targetAbs);

            return file_exists($targetAbs);
        } catch (\Throwable $e) {
            Log::error('convertToPdfCache exception', ['doc_id' => $document->id, 'e' => $e->getMessage()]);
            return false;
        }
    }
}
