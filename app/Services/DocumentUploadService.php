<?php

namespace App\Services;

use App\Data\UploadDocumentRequest;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadService
{
    public function handle(UploadDocumentRequest $req): int
    {
        return DB::transaction(function () use ($req) {
            $count = 0;

            // 1) Local files
            foreach ($req->files as $i => $file) {
                $title   = $this->pickTitle($req, $i, $file?->getClientOriginalName());
                $isActif = $this->pickStatus($req, $i);

                $stored = $this->storeLocalFile($file, $title);

                Document::create([
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

                    'converted_from'     => $stored['converted_from'] ?? null,
                    'converted_at'       => $stored['converted_at'] ?? null,

                    'is_actif'       => $isActif,
                    'view_count'     => 0,
                    'download_count' => 0,
                ]);

                $count++;
            }

            // 2) External links
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

    private function storeLocalFile($file, string $title): array
    {
        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $ext = $ext ?: 'bin';

        $slug = Str::slug($title) ?: 'document';
        $name = time() . '_' . $slug . '_' . Str::random(6) . '.' . $ext;

        $path = Storage::disk('public')->putFileAs('documents', $file, $name);

        return [
            'file_path'          => $path,
            'file_size'          => (int) ($file->getSize() ?: 0),
            'file_type'          => $this->resolveFileType($ext, $file->getMimeType()),
            'original_filename'  => $originalName,
            'original_extension' => $ext,
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
