<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use Livewire\Component;
use App\Models\Programme;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use App\Data\UploadDocumentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use App\Services\DocumentUploadService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DocumentUpload extends Component
{
    use WithFileUploads, LivewireAlert;

    public string $source_url = '';
    public const MAX_FILES = 10;
    private const MAX_BYTES = 10485760; // 10MB

    public $niveaux;
    public $ues;
    public $ecs;

    public ?int $niveau_id = null;
    public ?int $ue_id = null;
    public ?int $ec_id = null;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    public string $source = 'local'; // local | link
    public string $linkInput = '';
    public array $links = [];

    public array $titles = [];
    public array $statuses = [];

    /** Extensions autorisées (liens + local) */
    private array $allowedExtensions = [
        'pdf','doc','docx','ppt','pptx','xls','xlsx','jpg','jpeg','png'
    ];

    /** Mapping Content-Type => extension */
    private array $mimeToExt = [
        'application/pdf' => 'pdf',

        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',

        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',

        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',

        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public function mount(): void
    {
        $this->niveaux = Niveau::query()->where('status', true)->orderBy('name')->get();
        $this->ues = collect();
        $this->ecs = collect();
    }

    public function updatedNiveauId($value): void
    {
        $this->ue_id = null;
        $this->ec_id = null;

        $this->ues = $value
            ? Programme::query()
                ->active()
                ->ues()
                ->where('niveau_id', $value)
                ->orderBy('order')
                ->get()
            : collect();

        $this->ecs = collect();
        $this->resetValidation();
    }

    public function updatedUeId($value): void
    {
        $this->ec_id = null;

        $this->ecs = $value
            ? Programme::query()
                ->active()
                ->ecs()
                ->where('parent_id', $value)
                ->orderBy('order')
                ->get()
            : collect();

        $this->resetValidation();
    }

    public function updatedSource($value): void
    {
        $this->resetValidation();

        $this->titles = [];
        $this->statuses = [];

        if ($value === 'local') {
            $this->links = [];
            $this->linkInput = '';
            $this->source_url = '';
        } else {
            $this->files = [];
        }
    }

    public function updatedFiles(): void
    {
        if (!is_array($this->files)) return;

        if (count($this->files) > self::MAX_FILES) {
            $this->files = array_slice($this->files, 0, self::MAX_FILES);
        }

        foreach ($this->files as $i => $file) {
            if (!isset($this->titles[$i])) {
                $this->titles[$i] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
            if (!isset($this->statuses[$i])) {
                $this->statuses[$i] = true;
            }
        }
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index], $this->titles[$index], $this->statuses[$index]);
        $this->files = array_values($this->files);
        $this->titles = array_values($this->titles);
        $this->statuses = array_values($this->statuses);
    }

    public function clearLocal(): void
    {
        $this->files = [];
        $this->titles = [];
        $this->statuses = [];
        $this->resetValidation();
    }

    public function clearLinks(): void
    {
        $this->links = [];
        $this->titles = [];
        $this->statuses = [];
        $this->linkInput = '';
        $this->source_url = '';
        $this->resetValidation();
    }

    public function addLink(): void
    {
        $this->validate([
            'linkInput' => ['required', 'url', 'max:2048'],
        ]);

        if (count($this->links) >= self::MAX_FILES) {
            $this->addError('links', 'Nombre maximum de fichiers atteint.');
            return;
        }

        $url = trim($this->linkInput);

        // Normalisation (Drive / Docs / Slides / Sheets)
        $url = $this->normalizeDownloadUrl($url);

        $this->links[] = $url;

        $index = count($this->links) - 1;
        $this->titles[$index] = $this->titles[$index] ?? ($this->guessTitleFromUrl($url) ?? ('Document ' . ($index + 1)));
        $this->statuses[$index] = $this->statuses[$index] ?? true;

        $this->linkInput = '';
        $this->resetValidation();
    }

    public function removeLink(int $index): void
    {
        unset($this->links[$index], $this->titles[$index], $this->statuses[$index]);
        $this->links = array_values($this->links);
        $this->titles = array_values($this->titles);
        $this->statuses = array_values($this->statuses);
        $this->resetValidation();
    }

    public function uploadDocuments(DocumentUploadService $service)
    {
        // 1) Règles communes
        $rules = [
            'niveau_id' => 'required|integer|exists:niveaux,id',
            'ue_id'     => 'required|integer|exists:programmes,id',
            'ec_id'     => 'nullable|integer|exists:programmes,id',
        ];

        // 2) Règles selon la source (on garde votre logique)
        if ($this->source === 'local') {
            $rules['files']   = 'required|array|min:1|max:' . self::MAX_FILES;
            $rules['files.*'] = 'file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png';
        } else {
            $rules['links']   = 'nullable|array|max:' . self::MAX_FILES;
            $rules['links.*'] = 'required|url|max:2048';
            $rules['source_url'] = 'nullable|string';
        }

        $this->validate($rules, [
            'files.required' => 'Veuillez ajouter au moins un fichier.',
        ]);

        // 3) Charger UE + contrôler type / niveau
        $ue = Programme::query()
            ->select('id','type','niveau_id','semestre_id','parcour_id')
            ->findOrFail((int) $this->ue_id);

        if ($ue->type !== 'UE') {
            $this->addError('ue_id', "Le programme choisi doit être une UE.");
            return;
        }

        if ((int) $ue->niveau_id !== (int) $this->niveau_id) {
            $this->addError('ue_id', "Cette UE n'appartient pas au niveau sélectionné.");
            return;
        }

        // 4) Si EC renseigné, vérifier qu'il appartient à l'UE
        if ($this->ec_id) {
            $ec = Programme::query()
                ->select('id','type','parent_id','niveau_id')
                ->findOrFail((int) $this->ec_id);

            if ($ec->type !== 'EC') {
                $this->addError('ec_id', "Le programme choisi doit être une EC.");
                return;
            }
            if ((int) $ec->parent_id !== (int) $this->ue_id) {
                $this->addError('ec_id', "Cette EC n'appartient pas à l'UE sélectionnée.");
                return;
            }
            if ((int) $ec->niveau_id !== (int) $this->niveau_id) {
                $this->addError('ec_id', "Cette EC n'appartient pas au niveau sélectionné.");
                return;
            }
        }

        // 5) Déduire parcour/semestre depuis l'UE
        $parcourId  = (int) ($ue->parcour_id ?? 0);
        $semestreId = (int) ($ue->semestre_id ?? 0);

        if ($parcourId <= 0 || $semestreId <= 0) {
            $this->addError('ue_id', "Impossible de déduire Parcours/Semestre depuis l'UE. Vérifie programmes.parcour_id et programmes.semestre_id.");
            return;
        }

        // 6) programme_id à stocker dans documents : EC si choisi, sinon UE
        $programmeId = (int) ($this->ec_id ?: $this->ue_id);

        // 7) Construire les URLs finales (links[] + textarea source_url)
        $urlsFromTextarea = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", (string) $this->source_url) ?: [])));
        $urls = array_values(array_unique(array_filter(array_merge($this->links ?? [], $urlsFromTextarea))));

        if ($this->source === 'link' && count($urls) === 0) {
            $this->addError('links', 'Veuillez ajouter au moins un lien.');
            return;
        }

        if ($this->source === 'local' && (!is_array($this->files) || count($this->files) === 0)) {
            $this->addError('files', 'Veuillez ajouter au moins un fichier.');
            return;
        }

        // 8) Appeler le service
        try {
            $req = new UploadDocumentRequest(
                uploadedBy: Auth::id(),
                niveauId: (int) $this->niveau_id,
                ueId: (int) $this->ue_id,
                ecId: $this->ec_id ? (int) $this->ec_id : null,
                parcourId: $parcourId,
                semestreId: $semestreId,
                programmeId: $programmeId,
                files: $this->source === 'local' ? ($this->files ?? []) : [],
                titles: $this->titles ?? [],
                statuses: $this->statuses ?? [],
                urls: $this->source === 'link' ? $urls : [],
            );

            $created = $service->handle($req);

        } catch (\Throwable $e) {
            logger()->error('uploadDocuments failed', [
                'user_id' => Auth::id(),
                'source'  => $this->source,
                'files'   => is_array($this->files) ? count($this->files) : 0,
                'links'   => is_array($urls) ? count($urls) : 0,
                'msg'     => $e->getMessage(),
            ]);

            $this->addError('global', $e->getMessage());
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => true,
                'text' => $e->getMessage(),
            ]);
            return;
        }

        // 9) Reset
        $this->reset(['files', 'links', 'titles', 'statuses', 'linkInput', 'source_url']);
        $this->ue_id = null;
        $this->ec_id = null;

        // 10) Alert SUCCESS (restauré)
        $message = "{$created} document(s) enregistré(s).";
        $this->alert('success', 'Succès', [
            'position' => 'top-end',
            'timer' => 3500,
            'toast' => true,
            'text' => $message,
        ]);

        return redirect()->route('document.teacher');
    }

    /* ===========================
       Helpers (vous les aviez déjà)
       =========================== */

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

    private function storeAndMaybeConvert(UploadedFile $file, string $title): array
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        $baseSlug = Str::slug($title) ?: 'document';
        $rand = Str::random(6);
        $timestamp = time();

        $needsPdf = in_array($ext, ['doc', 'docx', 'ppt', 'pptx'], true);

        if ($needsPdf) {
            $tempDir = storage_path('app/private/temp');
            if (!is_dir($tempDir)) @mkdir($tempDir, 0775, true);

            $baseName = "{$timestamp}_{$baseSlug}_{$rand}";
            $inputAbs = $tempDir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;

            @copy($file->getRealPath(), $inputAbs);

            $outDirAbs = storage_path('app/public/documents');
            if (!is_dir($outDirAbs)) @mkdir($outDirAbs, 0775, true);

            $this->convertToPdfLibreOffice($inputAbs, $outDirAbs);

            $pdfAbs = $outDirAbs . DIRECTORY_SEPARATOR . $baseName . '.pdf';
            if (!file_exists($pdfAbs)) {
                @unlink($inputAbs);
                throw new \RuntimeException("Conversion PDF échouée : fichier PDF non généré.");
            }

            $relative = 'documents/' . $baseName . '.pdf';
            $size = filesize($pdfAbs) ?: 0;

            @unlink($inputAbs);

            return [
                'file_path' => $relative,
                'extension' => 'pdf',
                'file_size' => $size,
            ];
        }

        $filename = "{$timestamp}_{$baseSlug}_{$rand}.{$ext}";
        $relative = Storage::disk('public')->putFileAs('documents', $file, $filename);
        $size = $file->getSize() ?: 0;

        return [
            'file_path' => $relative,
            'extension' => $ext ?: 'bin',
            'file_size' => $size,
        ];
    }

    private function convertToPdfLibreOffice(string $inputAbs, string $outputDirAbs): void
    {
        $profileBase = storage_path('app/lo-profile');
        if (!is_dir($profileBase)) @mkdir($profileBase, 0775, true);

        $profileDir = $profileBase . DIRECTORY_SEPARATOR . 'lo_' . Str::uuid()->toString();
        @mkdir($profileDir, 0775, true);

        $profileUri = 'file://' . $profileDir;

        $cmd = [
            'libreoffice',
            '--headless',
            '--nologo',
            '--nolockcheck',
            '--nodefault',
            '--norestore',
            '-env:UserInstallation=' . $profileUri,
            '--convert-to', 'pdf',
            '--outdir', $outputDirAbs,
            $inputAbs,
        ];

        $process = new Process($cmd, base_path(), null, null, 120);
        $process->mustRun();

        $this->deleteDirectory($profileDir);
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir);
        if ($items === false) return;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) $this->deleteDirectory($path);
            else @unlink($path);
        }
        @rmdir($dir);
    }

    private function buildUploadedFilesFromLinks(): array
    {
        $uploaded = [];

        foreach ($this->links as $url) {
            $download = $this->downloadToTemp($url, self::MAX_BYTES);

            $ext = strtolower(pathinfo($download['name'], PATHINFO_EXTENSION));

            if (!$ext) {
                throw new \RuntimeException("Extension non autorisée : . (impossible de détecter le type du fichier)");
            }

            if (!in_array($ext, $this->allowedExtensions, true)) {
                @unlink($download['path']);
                throw new \RuntimeException("Extension non autorisée : .$ext");
            }

            $uploaded[] = $this->makeUploadedFileFromPath($download['path'], $download['name']);
        }

        return $uploaded;
    }

    private function downloadToTemp(string $url, int $maxBytes): array
    {
        $this->guardExternalUrl($url);

        $tmpDir = storage_path('app/private/temp');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

        $tmpName = Str::random(28);
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;

        try {
            $head = Http::timeout(10)->head($url);
            $len = (int) ($head->header('Content-Length') ?? 0);
            if ($len > 0 && $len > $maxBytes) {
                throw new \RuntimeException("Fichier trop volumineux (max 10MB).");
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $response = Http::timeout(90)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->sink($tmpPath)
            ->get($url);

        if (!$response->successful()) {
            @unlink($tmpPath);
            throw new \RuntimeException("Téléchargement impossible (HTTP " . $response->status() . ").");
        }

        $size = @filesize($tmpPath) ?: 0;
        if ($size <= 0) {
            @unlink($tmpPath);
            throw new \RuntimeException("Téléchargement impossible (fichier vide).");
        }
        if ($size > $maxBytes) {
            @unlink($tmpPath);
            throw new \RuntimeException("Fichier trop volumineux (max 10MB).");
        }

        $contentType = strtolower((string) $response->header('Content-Type'));

        if (str_contains($contentType, 'text/html')) {
            @unlink($tmpPath);
            throw new \RuntimeException(
                "Le lien ne pointe pas vers un fichier téléchargeable (HTML). " .
                "Pour Google Docs/Slides/Sheets, utilise un lien partage public ou un lien export."
            );
        }

        $name = $this->filenameFromContentDisposition((string) $response->header('Content-Disposition'))
            ?? $this->guessFileNameFromUrl($url)
            ?? ("document_" . Str::random(8));

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!$ext) {
            $extFromMime = $this->extFromMime($contentType);
            if ($extFromMime) {
                $name .= '.' . $extFromMime;
                $ext = $extFromMime;
            }
        }

        if (!$ext) {
            @unlink($tmpPath);
            throw new \RuntimeException("Impossible de détecter l’extension du fichier (URL + headers).");
        }

        $name = preg_replace('~[^a-zA-Z0-9\.\-\_ ]~', '_', $name);

        return ['path' => $tmpPath, 'name' => $name];
    }

    private function filenameFromContentDisposition(string $cd): ?string
    {
        if (!$cd) return null;

        if (preg_match('~filename\*=(?:UTF-8\'\')?([^;]+)~i', $cd, $m)) {
            $v = trim($m[1], " \t\n\r\0\x0B\"'");
            return urldecode($v);
        }

        if (preg_match('~filename=([^;]+)~i', $cd, $m)) {
            return trim($m[1], " \t\n\r\0\x0B\"'");
        }

        return null;
    }

    private function extFromMime(string $contentType): ?string
    {
        $mime = trim(explode(';', $contentType)[0] ?? '');
        return $this->mimeToExt[$mime] ?? null;
    }

    private function makeUploadedFileFromPath(string $path, string $originalName): UploadedFile
    {
        $mime = @mime_content_type($path) ?: 'application/octet-stream';
        $symfony = new SymfonyUploadedFile($path, $originalName, $mime, null, true);
        return UploadedFile::createFromBase($symfony);
    }

    private function normalizeDownloadUrl(string $url): string
    {
        if (str_contains($url, 'drive.google.com')) {
            if (preg_match('~\/file\/d\/([^\/]+)~', $url, $m)) {
                $id = $m[1];
                return "https://drive.google.com/uc?export=download&id={$id}";
            }
            if (preg_match('~[?&]id=([^&]+)~', $url, $m)) {
                $id = $m[1];
                return "https://drive.google.com/uc?export=download&id={$id}";
            }
        }

        if (str_contains($url, 'docs.google.com/document/')) {
            if (preg_match('~\/document\/d\/([^\/]+)~', $url, $m)) {
                $id = $m[1];
                return "https://docs.google.com/document/d/{$id}/export?format=pdf";
            }
        }

        if (str_contains($url, 'docs.google.com/presentation/')) {
            if (preg_match('~\/presentation\/d\/([^\/]+)~', $url, $m)) {
                $id = $m[1];
                return "https://docs.google.com/presentation/d/{$id}/export/pdf";
            }
        }

        if (str_contains($url, 'docs.google.com/spreadsheets/')) {
            if (preg_match('~\/spreadsheets\/d\/([^\/]+)~', $url, $m)) {
                $id = $m[1];
                return "https://docs.google.com/spreadsheets/d/{$id}/export?format=pdf";
            }
        }

        if (str_contains($url, 'dropbox.com')) {
            $url = preg_replace('~\?dl=\d~', '', $url);
            return $url . (str_contains($url, '?') ? '&' : '?') . 'dl=1';
        }

        return $url;
    }

    private function guardExternalUrl(string $url): void
    {
        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? '';
        $host = $parts['host'] ?? '';

        if (!in_array($scheme, ['https', 'http'], true)) {
            throw new \RuntimeException("Lien invalide (http/https uniquement).");
        }
        if (!$host) {
            throw new \RuntimeException("Lien invalide (hôte manquant).");
        }

        $blockedHosts = ['localhost', '127.0.0.1'];
        if (in_array(strtolower($host), $blockedHosts, true)) {
            throw new \RuntimeException("Hôte non autorisé.");
        }
    }

    private function guessFileNameFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) return null;

        $base = basename($path);
        if (!$base || $base === '/' || $base === '.') return null;

        return preg_replace('~[^a-zA-Z0-9\.\-\_ ]~', '_', $base);
    }

    private function guessTitleFromUrl(string $url): ?string
    {
        $name = $this->guessFileNameFromUrl($url);
        if (!$name) return null;
        return pathinfo($name, PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.teacher.document-upload', [
            'niveaux' => $this->niveaux,
            'ues' => $this->ues,
            'ecs' => $this->ecs,
        ]);
    }
}
