<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Jobs\ConvertDocumentToPdf;
use Illuminate\Support\Facades\DB;
use App\Data\UploadDocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DocumentUploadService
{
    /**
     * Point d'entrée unique appelé depuis Livewire:
     * $service->handle(UploadDocumentRequest $req)
     */
    public function handle(UploadDocumentRequest $req): int
    {
        return DB::transaction(function () use ($req) {
            $created = 0;

            // 1) Local files
            if (!empty($req->files)) {
                foreach ($req->files as $i => $file) {
                    $title  = $this->cleanTitle($req->titles[$i] ?? ('Document ' . ($i + 1)));
                    $active = $this->toBool($req->statuses[$i] ?? true);

                    $doc = $this->storeLocalFile($req, $file, $title, $active);
                    $created++;

                    // Conversion async (doc/docx/ppt/pptx)
                    $ext = strtolower((string) ($doc->original_extension ?? $doc->extensionFromPath()));
                    if (in_array($ext, config('documents.convert_to_pdf', []), true)) {
                        $this->dispatchConversionJob($doc);
                    }
                }
            }

            // 2) External URLs
            if (!empty($req->urls)) {
                foreach (array_values($req->urls) as $i => $url) {
                    $title  = $this->cleanTitle($req->titles[$i] ?? $this->guessTitleFromUrl($url) ?? ('Lien ' . ($i + 1)));
                    $active = $this->toBool($req->statuses[$i] ?? true);

                    $this->storeExternalLink($req, $url, $title, $active);
                    $created++;
                }
            }

            return $created;
        });
    }

    // ---------------------------------------------------------------------
    // Local store
    // ---------------------------------------------------------------------
    private function storeLocalFile(UploadDocumentRequest $req, $file, string $title, bool $active): Document
    {
        // Nom original + extension
        $origName = '';
        try {
            $origName = (string) $file->getClientOriginalName();
        } catch (\Throwable $e) {
            $origName = 'document';
        }

        $ext = '';
        try {
            $ext = strtolower((string) $file->getClientOriginalExtension());
        } catch (\Throwable $e) {
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        }
        $ext = $ext ?: strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $ext = $ext ?: 'bin';

        // Nom fichier propre
        $slug = Str::slug($title) ?: 'document';
        $filename = now()->format('Ymd_His') . '_' . $slug . '_' . Str::random(6) . '.' . $ext;

        // Stockage public/documents
        $relativePath = $file->storeAs('documents', $filename, 'public');

        // Taille bytes (fiable après stockage)
        $sizeBytes = 0;
        try {
            $sizeBytes = (int) Storage::disk('public')->size($relativePath);
        } catch (\Throwable $e) {
            $sizeBytes = 0;
        }

        // Payload DB (on évite de casser si une colonne n'existe pas)
        $data = [
            'title'              => $title,
            'file_path'          => $relativePath,
            'source_url'         => null,
            'niveau_id'          => $req->niveauId,
            'parcour_id'         => $req->parcourId,
            'semestre_id'        => $req->semestreId,
            'uploaded_by'        => $req->uploadedBy,
            'is_actif'           => $active,
            'is_archive'         => false,
            'view_count'         => 0,
            'download_count'     => 0,
            'original_extension' => $ext,
            'converted_from'     => null,
            'file_size_bytes'    => $sizeBytes,
        ];

        // programme_id si présent dans la table
        if (Schema::hasColumn('documents', 'programme_id')) {
            $data['programme_id'] = $req->programmeId;
        }

        // Certains projets ont aussi ue_id/ec_id: on les set uniquement si colonnes existent
        if (Schema::hasColumn('documents', 'ue_id')) {
            $data['ue_id'] = $req->ueId;
        }
        if (Schema::hasColumn('documents', 'ec_id')) {
            $data['ec_id'] = $req->ecId;
        }

        $doc = new Document();
        $doc->forceFill($data);
        $doc->save();

        Log::info('Document local enregistré', [
            'document_id' => $doc->id,
            'path' => $relativePath,
            'size' => $sizeBytes,
            'ext'  => $ext,
        ]);

        return $doc;
    }

    private function dispatchConversionJob(Document $doc): void
    {
        try {
            // Copie vers un fichier temp stable pour le Job
            $publicPath = Storage::disk('public')->path($doc->file_path);
            if (!file_exists($publicPath)) return;

            $tmpDir = storage_path('app/temp-convert');
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0755, true);
            }

            $ext = strtolower((string) ($doc->original_extension ?? $doc->extensionFromPath()));
            $tmpPath = rtrim($tmpDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'doc_' . $doc->id . '_' . Str::random(8) . '.' . $ext;

            if (!@copy($publicPath, $tmpPath)) {
                Log::warning('Impossible de copier vers temp-convert', [
                    'doc_id' => $doc->id,
                    'from' => $publicPath,
                    'to' => $tmpPath,
                ]);
                return;
            }

            ConvertDocumentToPdf::dispatch($doc, $tmpPath);

            Log::info('Job conversion dispatch', [
                'doc_id' => $doc->id,
                'tmp' => $tmpPath,
            ]);
        } catch (\Throwable $e) {
            Log::error('dispatchConversionJob error', [
                'doc_id' => $doc->id,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    // ---------------------------------------------------------------------
    // External store (lecture, pas download)
    // ---------------------------------------------------------------------
    private function storeExternalLink(UploadDocumentRequest $req, string $url, string $title, bool $active): Document
    {
        $url = trim($url);

        // ✅ Normaliser en "READ URL" (évite que Google Drive PDF télécharge directement)
        $readUrl = $this->normalizeToReadUrl($url);

        $extGuess = $this->guessExtensionFromUrl($readUrl);

        $data = [
            'title'              => $title,
            'file_path'          => $readUrl, // on garde une seule source
            'source_url'         => $readUrl,
            'niveau_id'          => $req->niveauId,
            'parcour_id'         => $req->parcourId,
            'semestre_id'        => $req->semestreId,
            'uploaded_by'        => $req->uploadedBy,
            'is_actif'           => $active,
            'is_archive'         => false,
            'view_count'         => 0,
            'download_count'     => 0, // ⚠️ tu ne vas PAS incrémenter les downloads pour externe côté controller
            'original_extension' => $extGuess ?: 'link',
            'converted_from'     => null,
            'file_size_bytes'    => 0, // inconnu pour un lien (tu peux afficher "—" côté UI)
        ];

        if (Schema::hasColumn('documents', 'programme_id')) {
            $data['programme_id'] = $req->programmeId;
        }
        if (Schema::hasColumn('documents', 'ue_id')) {
            $data['ue_id'] = $req->ueId;
        }
        if (Schema::hasColumn('documents', 'ec_id')) {
            $data['ec_id'] = $req->ecId;
        }

        $doc = new Document();
        $doc->forceFill($data);
        $doc->save();

        Log::info('Document externe enregistré', [
            'document_id' => $doc->id,
            'url' => $readUrl,
            'ext' => $extGuess,
        ]);

        return $doc;
    }

    // ---------------------------------------------------------------------
    // URL Normalizers (READ)
    // ---------------------------------------------------------------------
    private function normalizeToReadUrl(string $url): string
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

        // Dropbox: dl=1 => download. Pour lecture: dl=0
        if (str_contains($host, 'dropbox.com')) {
            $url = preg_replace('~(\?|&)dl=\d~', '$1dl=0', $url) ?? $url;
            if (!str_contains($url, 'dl=0')) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'dl=0';
            }
            return $url;
        }

        // Google Drive: uc?export=download&id=XXX => file/d/XXX/view
        if (str_contains($host, 'drive.google.com')) {
            if (preg_match('~[?&]id=([^&]+)~', $url, $m)) {
                $id = $m[1];
                return "https://drive.google.com/file/d/{$id}/view?usp=sharing";
            }
            if (preg_match('~/file/d/([^/]+)~', $path, $m)) {
                $id = $m[1];
                return "https://drive.google.com/file/d/{$id}/view?usp=sharing";
            }
            if (preg_match('~/uc~', $path) && preg_match('~[?&]export=download~', $url) && preg_match('~[?&]id=([^&]+)~', $url, $m)) {
                $id = $m[1];
                return "https://drive.google.com/file/d/{$id}/view?usp=sharing";
            }
        }

        // Google Docs export => edit
        if (str_contains($host, 'docs.google.com')) {
            if (preg_match('~/document/d/([^/]+)~', $path, $m)) {
                $id = $m[1];
                return "https://docs.google.com/document/d/{$id}/edit";
            }
            if (preg_match('~/presentation/d/([^/]+)~', $path, $m)) {
                $id = $m[1];
                return "https://docs.google.com/presentation/d/{$id}/edit";
            }
            if (preg_match('~/spreadsheets/d/([^/]+)~', $path, $m)) {
                $id = $m[1];
                return "https://docs.google.com/spreadsheets/d/{$id}/edit";
            }
        }

        return $url;
    }

    private function guessExtensionFromUrl(string $url): ?string
    {
        $path = strtolower((string) (parse_url($url, PHP_URL_PATH) ?? ''));
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext) return $ext;

        // Ex: docs export?format=pdf
        $query = (string) (parse_url($url, PHP_URL_QUERY) ?? '');
        parse_str($query, $q);

        if (!empty($q['format']) && is_string($q['format'])) {
            return strtolower($q['format']);
        }

        return null;
    }

    private function guessTitleFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) return null;

        $base = basename($path);
        if (!$base || $base === '/' || $base === '.') return null;

        $title = pathinfo($base, PATHINFO_FILENAME);
        $title = trim(preg_replace('/\s+/', ' ', $title) ?? $title);

        return $title !== '' ? Str::limit($title, 180, '') : null;
    }

    // ---------------------------------------------------------------------
    // Utils
    // ---------------------------------------------------------------------
    private function cleanTitle(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        $value = Str::limit($value, 180, '');
        return $value !== '' ? $value : 'Document';
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1;
        if (is_string($value)) return in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true);
        return (bool) $value;
    }


     /**
     * Support Livewire TemporaryUploadedFile (extends UploadedFile).
     * Ne modifie pas la logique de handle(UploadDocumentRequest $req).
     */
    public function handleUploadedFile(
        UploadedFile $file,
        string $disk = 'public',
        string $directory = 'documents'
    ): array {
        // Stockage
        $storedPath = $file->store($directory, $disk);

        // Métadonnées
        $originalName = (string) ($file->getClientOriginalName() ?? '');
        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: pathinfo($storedPath, PATHINFO_EXTENSION)));
        $size = (int) ($file->getSize() ?? 0);

        return [
            'file_path' => $storedPath,
            'original_filename' => $originalName !== '' ? $originalName : null,
            'original_extension' => $ext !== '' ? $ext : null,
            'converted_from' => null,
            'file_size_bytes' => $size > 0 ? $size : null,
        ];
    }
}
