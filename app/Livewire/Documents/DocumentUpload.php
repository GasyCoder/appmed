<?php

namespace App\Livewire\Documents;

use App\Data\UploadDocumentRequest;
use App\Models\Niveau;
use App\Models\Programme;
use App\Services\DocumentUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentUpload extends Component
{
    use WithFileUploads, LivewireAlert;

    public const MAX_FILES = 10;

    // UI/meta
    public array $fileMeta = []; // [index => ['tmp','name','ext','will_convert','size_bytes','size_human']]
    public string $maxUploadSize = '';
    public int $maxUploadBytes = 0;

    // source
    public string $source = 'local'; // local | link
    public string $source_url = '';  // textarea multi-lines
    public string $linkInput = '';   // single url input
    public array $links = [];        // array of urls

    // selects
    public $niveaux;
    public $ues;
    public $ecs;

    public ?int $niveau_id = null;
    public ?int $ue_id = null;
    public ?int $ec_id = null;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    /** @var array<int, string> */
    public array $titles = [];

    /** @var array<int, bool|int|string> */
    public array $statuses = [];

    /** Extensions autorisées (local) */
    private array $allowedExtensions = [
        'pdf','doc','docx','ppt','pptx','xls','xlsx','jpg','jpeg','png'
    ];

    public function mount(): void
    {
        $this->niveaux = Niveau::query()->where('status', true)->orderBy('name')->get();
        $this->ues = collect();
        $this->ecs = collect();

        $this->maxUploadBytes = $this->getMaxUploadBytes();
        $this->maxUploadSize  = $this->formatBytes($this->maxUploadBytes);

        Log::info('Upload limits', [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size'       => ini_get('post_max_size'),
            'maxUploadBytes'      => $this->maxUploadBytes,
            'maxUploadSize'       => $this->maxUploadSize,
            'livewire_disk'       => config('livewire.temporary_file_upload.disk'),
            'livewire_directory'  => config('livewire.temporary_file_upload.directory'),
        ]);
    }

    // ---------------------------------------------------------------------
    // Helpers tailles / encodage
    // ---------------------------------------------------------------------

    private function getMaxUploadBytes(): int
    {
        $upload = (string) ini_get('upload_max_filesize');
        $post   = (string) ini_get('post_max_size');

        $uploadBytes = $this->parseSize($upload);
        $postBytes   = $this->parseSize($post);

        $min = min($uploadBytes, $postBytes);
        return $min > 0 ? $min : 10 * 1024 * 1024;
    }

    private function parseSize(string $size): int
    {
        $size = trim($size);
        if ($size === '') return 0;

        $unit  = strtolower(substr($size, -1));
        $value = (int) $size;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $size,
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1, '.', '') . ' Go';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 1, '.', '') . ' Mo';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 1, '.', '') . ' Ko';
        return $bytes . ' octets';
    }

    /**
     * ✅ Texte sûr UTF-8 (évite "Malformed UTF-8" dans les réponses Livewire JSON)
     */
    private function safeUtf8(mixed $value): string
    {
        if ($value === null) return '';
        if (is_bool($value) || is_int($value) || is_float($value)) return (string) $value;

        $s = (string) $value;
        $s = str_replace("\0", '', $s);

        if (!mb_check_encoding($s, 'UTF-8')) {
            $s = @mb_convert_encoding($s, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        }

        $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $s);
        return trim(($fixed !== false && $fixed !== null) ? $fixed : '');
    }

    /**
     * ✅ Taille SAFE: ne JAMAIS appeler TemporaryUploadedFile::getSize()
     * (source du warning Flysystem "Unable to retrieve file_size ... documents-tmp")
     */
    private function safeTmpSize($file): ?int
    {
        try {
            // 1) realPath -> filesize (le plus fiable)
            try {
                $real = $file->getRealPath();
                if ($real && is_file($real)) {
                    $s = @filesize($real);
                    return $s !== false ? (int) $s : null;
                }
            } catch (\Throwable $e) {}

            // 2) pathname -> filesize (fallback)
            try {
                $path = $file->getPathname();
                if ($path && is_file($path)) {
                    $s = @filesize($path);
                    return $s !== false ? (int) $s : null;
                }
            } catch (\Throwable $e) {}
        } catch (\Throwable $e) {}

        return null;
    }

    // ---------------------------------------------------------------------
    // Selects
    // ---------------------------------------------------------------------

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
        $this->fileMeta = [];

        if ($value === 'local') {
            $this->links = [];
            $this->linkInput = '';
            $this->source_url = '';
        } else {
            $this->files = [];
        }
    }

    // ---------------------------------------------------------------------
    // Files
    // ---------------------------------------------------------------------

    public function updatedFiles(): void
    {
        if ($this->source !== 'local') return;

        if (!is_array($this->files)) {
            $this->files = [];
            $this->fileMeta = [];
            return;
        }

        // ✅ Cap
        if (count($this->files) > self::MAX_FILES) {
            $this->alert('warning', 'Trop de fichiers', [
                'position' => 'top-end',
                'timer' => 3500,
                'toast' => true,
                'text' => 'Maximum ' . self::MAX_FILES . ' fichiers. Les excédents ont été retirés.',
            ]);

            $this->files = array_slice($this->files, 0, self::MAX_FILES);
            $this->files = array_values($this->files);
        }

        // ✅ Rebuild meta (plus fiable que des unset partiels)
        $this->fileMeta = [];

        foreach ($this->files as $index => $file) {
            if (!is_object($file)) continue;

            // Erreurs upload PHP (si dispo)
            try {
                if (method_exists($file, 'getError')) {
                    $error = $file->getError();

                    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                        $this->rejectFile($index, "Le fichier dépasse la limite du serveur ({$this->maxUploadSize}).");
                        return;
                    }

                    if ($error === UPLOAD_ERR_PARTIAL) {
                        $this->rejectFile($index, "Upload incomplet. Vérifiez la connexion et réessayez.");
                        return;
                    }

                    if ($error !== UPLOAD_ERR_OK && $error !== 0) {
                        $this->rejectFile($index, "Erreur upload (code {$error}). Réessayez.");
                        return;
                    }
                }
            } catch (\Throwable $e) {
                // on ignore, on ne casse pas l'upload
            }

            // Nom original (safe)
            $name = 'document';
            try { $name = (string) $file->getClientOriginalName(); } catch (\Throwable $e) {}
            $name = $this->safeUtf8($name);

            // Extension (safe)
            $ext = '';
            try { $ext = strtolower((string) $file->getClientOriginalExtension()); } catch (\Throwable $e) {}
            $ext = $ext ?: '—';

            $willConvert = in_array($ext, ['doc','docx','ppt','pptx'], true);

            // ✅ Taille SAFE (aucun getSize)
            $size = $this->safeTmpSize($file);

            // Limite si taille connue
            if (is_int($size) && $size > 0 && $size > $this->maxUploadBytes) {
                $this->rejectFile($index, "Fichier trop volumineux (" . $this->formatBytes($size) . "). Limite : {$this->maxUploadSize}.");
                return;
            }

            // tmp id (clé stable pour wire:key)
            $tmp = '';
            try { $tmp = (string) $file->getFilename(); } catch (\Throwable $e) {}
            $tmp = $tmp !== '' ? $tmp : (string) $index;

            $this->fileMeta[$index] = [
                'tmp' => $tmp,
                'name' => $name,
                'ext' => $ext,
                'will_convert' => $willConvert,
                'size_bytes' => (is_int($size) && $size > 0) ? $size : null,
                'size_human' => (is_int($size) && $size > 0) ? $this->formatBytes($size) : '—',
            ];
        }

        // ✅ Préremplir titres/statuts
        foreach ($this->files as $i => $file) {
            if (!isset($this->titles[$i]) || trim((string) $this->titles[$i]) === '') {
                $fallbackTitle = 'Document ' . ($i + 1);
                try {
                    $base = (string) $file->getClientOriginalName();
                    $fallbackTitle = pathinfo($base, PATHINFO_FILENAME) ?: $fallbackTitle;
                } catch (\Throwable $e) {}
                $this->titles[$i] = $this->safeUtf8($fallbackTitle);
            }

            if (!isset($this->statuses[$i])) {
                $this->statuses[$i] = true;
            }

            if (!isset($this->fileMeta[$i])) {
                $this->fileMeta[$i] = [
                    'tmp' => (string) $i,
                    'name' => 'document',
                    'ext' => '—',
                    'will_convert' => false,
                    'size_bytes' => null,
                    'size_human' => '—',
                ];
            }
        }
    }

    private function rejectFile(int $index, string $message): void
    {
        $message = $this->safeUtf8($message);

        $this->alert('error', 'Erreur upload', [
            'position' => 'center',
            'timer' => 0,
            'toast' => false,
            'showConfirmButton' => true,
            'text' => $message,
        ]);

        // ✅ IMPORTANT: retirer le fichier du state Livewire
        $this->removeFile($index);
    }

    public function removeFile(int $index): void
    {
        if (!isset($this->files[$index])) return;

        unset($this->files[$index], $this->titles[$index], $this->statuses[$index], $this->fileMeta[$index]);

        $this->files = array_values($this->files);
        $this->titles = array_values($this->titles);
        $this->statuses = array_values($this->statuses);
        $this->fileMeta = array_values($this->fileMeta);

        // ✅ Rebuild meta après reindex (évite mismatch indices)
        $this->updatedFiles();
    }

    public function clearLocal(): void
    {
        $this->files = [];
        $this->titles = [];
        $this->statuses = [];
        $this->fileMeta = [];
        $this->resetValidation();
    }

    // ---------------------------------------------------------------------
    // Links
    // ---------------------------------------------------------------------

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
            $this->addError('links', 'Nombre maximum de liens atteint.');
            return;
        }

        $url = trim($this->linkInput);
        $url = $this->sanitizeUrl($url);

        $this->links[] = $url;

        $index = count($this->links) - 1;
        $this->titles[$index] = $this->titles[$index] ?? ($this->guessTitleFromUrl($url) ?? ('Document ' . ($index + 1)));
        $this->titles[$index] = $this->safeUtf8((string) $this->titles[$index]);

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

    private function sanitizeUrl(string $url): string
    {
        $url = $this->safeUtf8($url);
        return trim($url);
    }

    private function guessFileNameFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) return null;

        $base = basename($path);
        if (!$base || $base === '/' || $base === '.') return null;

        $base = preg_replace('~[^a-zA-Z0-9\.\-\_ ]~', '_', $base) ?? $base;
        return $this->safeUtf8($base);
    }

    private function guessTitleFromUrl(string $url): ?string
    {
        $name = $this->guessFileNameFromUrl($url);
        if (!$name) return null;

        $title = pathinfo($name, PATHINFO_FILENAME);
        $title = $this->safeUtf8($title);
        return $title !== '' ? $title : null;
    }

    // ---------------------------------------------------------------------
    // Upload handler
    // ---------------------------------------------------------------------

    public function uploadDocuments(DocumentUploadService $service)
    {
        $rules = [
            'niveau_id' => 'required|integer|exists:niveaux,id',
            'ue_id'     => 'required|integer|exists:programmes,id',
            'ec_id'     => 'nullable|integer|exists:programmes,id',
        ];

        if ($this->source === 'local') {
            $rules['files']   = 'required|array|min:1|max:' . self::MAX_FILES;
            $maxKb = (int) floor($this->maxUploadBytes / 1024);
            $rules['files.*'] = "file|max:{$maxKb}|mimes:" . implode(',', $this->allowedExtensions);
        } else {
            $rules['links']   = 'nullable|array|max:' . self::MAX_FILES;
            $rules['links.*'] = 'required|url|max:2048';
            $rules['source_url'] = 'nullable|string';
        }

        $messages = [
            'niveau_id.required' => 'Veuillez sélectionner un niveau.',
            'ue_id.required' => 'Veuillez sélectionner une UE.',
            'files.required' => 'Veuillez ajouter au moins un fichier.',
            'files.*.max' => 'Un fichier dépasse la taille maximale (' . $this->maxUploadSize . ').',
            'files.*.mimes' => 'Format non autorisé. Acceptés: PDF, Word, PowerPoint, Excel, Images.',
            'files.*.uploaded' => 'Le fichier n\'a pas pu être téléversé. Taille maximale : ' . $this->maxUploadSize . '.',
            'files.*.file' => 'Le fichier sélectionné n\'est pas valide.',
        ];

        $this->validate($rules, $messages);

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

        $parcourId  = (int) ($ue->parcour_id ?? 0);
        $semestreId = (int) ($ue->semestre_id ?? 0);

        if ($parcourId <= 0 || $semestreId <= 0) {
            $this->addError('ue_id', "Impossible de déduire Parcours/Semestre depuis l'UE.");
            return;
        }

        $programmeId = (int) ($this->ec_id ?: $this->ue_id);

        $urlsFromTextarea = array_values(array_filter(array_map(
            'trim',
            preg_split("/\r\n|\n|\r/", (string) $this->source_url) ?: []
        )));
        $urls = array_values(array_unique(array_filter(array_merge($this->links ?? [], $urlsFromTextarea))));

        if ($this->source === 'link' && count($urls) === 0) {
            $this->addError('links', 'Veuillez ajouter au moins un lien.');
            return;
        }

        // ✅ sécuriser titres (UTF-8)
        $titles = array_map(fn ($t) => $this->safeUtf8((string) $t), $this->titles);

        try {
            $req = new UploadDocumentRequest(
                uploadedBy: (int) Auth::id(),
                niveauId: (int) $this->niveau_id,
                ueId: (int) $this->ue_id,
                ecId: $this->ec_id ? (int) $this->ec_id : null,
                programmeId: $programmeId,
                parcourId: $parcourId,
                semestreId: $semestreId,
                files: $this->source === 'local' ? ($this->files ?? []) : [],
                titles: $titles,
                statuses: $this->statuses ?? [],
                urls: $this->source === 'link' ? $urls : [],
            );

            // ✅ service->handle doit exister
            $created = $service->handle($req);

        } catch (\Throwable $e) {
            $raw  = (string) $e->getMessage();
            $safe = $this->safeUtf8($raw);
            $safe = $safe !== '' ? Str::limit($safe, 300, '...') : "Erreur lors de l'upload. Réessayez.";

            Log::error('uploadDocuments failed', [
                'user_id' => Auth::id(),
                'source'  => $this->source,
                'files_count' => is_array($this->files) ? count($this->files) : 0,
                'links_count' => is_array($urls) ? count($urls) : 0,
                'msg'     => $raw,
            ]);

            $this->addError('global', $safe);

            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 0,
                'toast' => false,
                'showConfirmButton' => true,
                'text' => $safe,
            ]);

            return;
        }

        $this->reset(['files', 'links', 'titles', 'statuses', 'linkInput', 'source_url', 'fileMeta']);
        $this->ue_id = null;
        $this->ec_id = null;

        $this->alert('success', 'Succès', [
            'position' => 'top-end',
            'timer' => 3500,
            'toast' => true,
            'text' => "{$created} document(s) enregistré(s).",
        ]);

        return redirect()->route('documents.index');
    }

    public function render()
    {
        return view('livewire.documents.document-upload', [
            'niveaux' => $this->niveaux,
            'ues'     => $this->ues,
            'ecs'     => $this->ecs,
        ]);
    }
}
