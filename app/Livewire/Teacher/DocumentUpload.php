<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Programme;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Data\UploadDocumentRequest;
use App\Services\DocumentUploadService;

class DocumentUpload extends Component
{
    use WithFileUploads, LivewireAlert;

    public const MAX_FILES = 10;

    // Limite max externe (si tu télécharges depuis un lien)
    private const MAX_BYTES = 536870912; // 512MB

    public string $source_url = '';
    public string $source = 'local'; // local | link
    public string $linkInput = '';
    public array $links = [];

    public $niveaux;
    public $ues;
    public $ecs;

    public ?int $niveau_id = null;
    public ?int $ue_id = null;
    public ?int $ec_id = null;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    public array $titles = [];
    public array $statuses = [];

    // Infos UI sur limites
    public string $maxUploadSize = '';
    public int $maxUploadBytes = 0;

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

        // Limite serveur PHP
        $this->maxUploadBytes = $this->getMaxUploadBytes();
        $this->maxUploadSize = $this->formatBytes($this->maxUploadBytes);

        Log::info('Upload limits', [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size'       => ini_get('post_max_size'),
            'maxUploadBytes'      => $this->maxUploadBytes,
            'maxUploadSize'       => $this->maxUploadSize,
            'livewire_disk'       => config('livewire.temporary_file_upload.disk'),
            'livewire_directory'  => config('livewire.temporary_file_upload.directory'),
        ]);
    }

    // ---------------------------
    // Helpers: server limits
    // ---------------------------
    private function getMaxUploadBytes(): int
    {
        $upload = (string) ini_get('upload_max_filesize');
        $post   = (string) ini_get('post_max_size');

        $uploadBytes = $this->parseSize($upload);
        $postBytes   = $this->parseSize($post);

        $min = min($uploadBytes, $postBytes);

        // fallback sécurité
        return $min > 0 ? $min : 10 * 1024 * 1024;
    }

    private function parseSize(string $size): int
    {
        $size = trim($size);
        if ($size === '') return 0;

        $unit = strtolower(substr($size, -1));
        $value = (int) $size;

        return match($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $size,
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' Go';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 1) . ' Mo';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 1) . ' Ko';
        return $bytes . ' octets';
    }

    // ---------------------------
    // Selects
    // ---------------------------
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

    // ---------------------------
    // ✅ FIX IMPORTANT : updatedFiles SAFE
    // ---------------------------
    public function updatedFiles(): void
    {
        if (!is_array($this->files)) return;

        foreach ($this->files as $index => $file) {
            if (!is_object($file)) continue;

            // 1) Vérifier les erreurs upload PHP si disponibles
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

            // 2) ✅ Lire la taille (SAFE) => ne jamais crasher
            $size = null;
            try {
                $size = (int) $file->getSize();
            } catch (\Throwable $e) {
                Log::warning('Unable to retrieve tmp file size (Livewire)', [
                    'user_id' => Auth::id(),
                    'index' => $index,
                    'tmp_disk' => config('livewire.temporary_file_upload.disk'),
                    'tmp_dir'  => config('livewire.temporary_file_upload.directory'),
                    'file_class' => get_class($file),
                    'msg' => $e->getMessage(),
                ]);

                $this->rejectFile(
                    $index,
                    "Le serveur n'arrive pas à lire le fichier temporaire (permissions ou upload interrompu). Réessayez."
                );
                return;
            }

            // 3) Vérifier taille max serveur
            if ($size > $this->maxUploadBytes) {
                $human = $this->formatBytes($size);
                $this->rejectFile($index, "Fichier trop volumineux ({$human}). Limite : {$this->maxUploadSize}.");
                return;
            }
        }

        // 4) Limiter nombre fichiers
        if (count($this->files) > self::MAX_FILES) {
            $this->alert('warning', 'Trop de fichiers', [
                'position' => 'top-end',
                'timer' => 3500,
                'toast' => true,
                'text' => 'Maximum ' . self::MAX_FILES . ' fichiers. Les fichiers excédentaires ont été retirés.',
            ]);

            $this->files = array_slice($this->files, 0, self::MAX_FILES);
        }

        // 5) Pré-remplir titres/statuts
        foreach ($this->files as $i => $file) {
            if (!isset($this->titles[$i])) {
                $this->titles[$i] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
            if (!isset($this->statuses[$i])) {
                $this->statuses[$i] = true;
            }
        }
    }

    private function rejectFile(int $index, string $message): void
    {
        $name = '';
        try {
            $name = $this->files[$index]?->getClientOriginalName() ?? '';
        } catch (\Throwable $e) {
            $name = '';
        }

        $text = $name ? "« {$name} » : {$message}" : $message;

        $this->alert('error', 'Erreur upload', [
            'position' => 'center',
            'timer' => 0,
            'toast' => false,
            'showConfirmButton' => true,
            'text' => $text,
        ]);

        unset($this->files[$index]);
        $this->files = array_values($this->files);
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
        // Règles communes
        $rules = [
            'niveau_id' => 'required|integer|exists:niveaux,id',
            'ue_id'     => 'required|integer|exists:programmes,id',
            'ec_id'     => 'nullable|integer|exists:programmes,id',
        ];

        if ($this->source === 'local') {
            $rules['files']   = 'required|array|min:1|max:' . self::MAX_FILES;
            $maxKb = floor($this->maxUploadBytes / 1024);
            $rules['files.*'] = "file|max:{$maxKb}|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png";
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

        // Vérifs UE/EC
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

        // URLs depuis textarea + links[]
        $urlsFromTextarea = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", (string) $this->source_url) ?: [])));
        $urls = array_values(array_unique(array_filter(array_merge($this->links ?? [], $urlsFromTextarea))));

        if ($this->source === 'link' && count($urls) === 0) {
            $this->addError('links', 'Veuillez ajouter au moins un lien.');
            return;
        }

        try {
            $req = new UploadDocumentRequest(
                uploadedBy: Auth::id(),
                niveauId: (int) $this->niveau_id,
                ueId: (int) $this->ue_id,
                ecId: $this->ec_id ? (int) $this->ec_id : null,
                programmeId: $programmeId,
                parcourId: $parcourId,
                semestreId: $semestreId,
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
                'files_count' => is_array($this->files) ? count($this->files) : 0,
                'links_count' => is_array($urls) ? count($urls) : 0,
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

        // Reset
        $this->reset(['files', 'links', 'titles', 'statuses', 'linkInput', 'source_url']);
        $this->ue_id = null;
        $this->ec_id = null;

        $this->alert('success', 'Succès', [
            'position' => 'top-end',
            'timer' => 3500,
            'toast' => true,
            'text' => "{$created} document(s) enregistré(s).",
        ]);

        return redirect()->route('document.teacher');
    }

    // ---------------------------
    // Link helpers (inchangés)
    // ---------------------------
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
            'ues'     => $this->ues,
            'ecs'     => $this->ecs,
        ]);
    }
}
