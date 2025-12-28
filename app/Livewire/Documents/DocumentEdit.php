<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Programme;
use App\Services\DocumentUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DocumentEdit extends Component
{
    use WithFileUploads, AuthorizesRequests, LivewireAlert;

    public Document $document;

    public string $title = '';
    public string $niveau_id = '';

    // UE/EC = Programme
    public string $ue_id = ''; // programme UE id
    public string $ec_id = ''; // programme EC id (optional)

    public bool $is_actif = false;
    public bool $is_archive = false;

    // keep/file/link
    public string $replace_mode = 'keep';

    public string $source_url = '';
    public $file = null;

    /**
     * Permet de savoir si on peut auto-mettre à jour le titre.
     * Si l'utilisateur a un titre custom, on ne l'écrase pas.
     */
    public string $autoTitleBaseline = ''; // "titre auto" basé sur l'ancien fichier/lien
    public bool $titleWasManuallyEdited = false;

    public function mount(Document $document): void
    {
        $this->document = $document;
        $this->authorize('update', $this->document);

        $this->title = (string) $document->title;
        $this->niveau_id = (string) $document->niveau_id;

        $this->is_actif = (bool) $document->is_actif;
        $this->is_archive = (bool) $document->is_archive;

        // source actuelle
        $currentExternal = $document->externalUrl();
        $this->source_url = (string) ($currentExternal ?? $document->source_url ?? '');

        $this->replace_mode = $document->isExternalLink() ? 'link' : 'keep';

        // Base "auto title" => sert à décider si on peut écraser le title lors d'un nouveau fichier
        $this->autoTitleBaseline = $this->guessTitleFromExistingDocument($document);
        $this->titleWasManuallyEdited = false;

        // Pré-remplir UE/EC depuis programme_id
        $pid = (int) ($document->programme_id ?? 0);
        if ($pid > 0) {
            $p = Programme::query()->select(['id','type','parent_id','niveau_id','semestre_id'])->find($pid);
            if ($p) {
                $this->niveau_id = (string) ($p->niveau_id ?? $document->niveau_id);

                if ($p->type === Programme::TYPE_EC && $p->parent_id) {
                    $this->ue_id = (string) $p->parent_id;
                    $this->ec_id = (string) $p->id;
                } elseif ($p->type === Programme::TYPE_UE) {
                    $this->ue_id = (string) $p->id;
                    $this->ec_id = '';
                }
            }
        }
    }

    /**
     * Si l'utilisateur tape réellement un titre, on le considère "manuel"
     * => on évite de l'écraser quand un fichier est sélectionné.
     */
    public function updatedTitle($value): void
    {
        $value = trim((string) $value);

        // Si le titre devient différent de la baseline auto, on lock en "manuel"
        if ($value !== '' && $value !== $this->autoTitleBaseline) {
            $this->titleWasManuallyEdited = true;
        }

        // Si l'utilisateur remet EXACTEMENT le même que baseline, on peut re-autoriser
        if ($value === $this->autoTitleBaseline) {
            $this->titleWasManuallyEdited = false;
        }
    }

    public function updatedNiveauId(): void
    {
        $this->ue_id = '';
        $this->ec_id = '';
    }

    public function updatedUeId(): void
    {
        $this->ec_id = '';
    }

    /**
     * IMPORTANT: quand on choisit un fichier, on force replace_mode=file
     * et on met à jour le titre automatiquement si on n'a pas un titre "manuel".
     */
    public function updatedFile(): void
    {
        if (!$this->file) return;

        $this->replace_mode = 'file';

        // Suggestion basée sur le nouveau fichier
        $newSuggested = $this->titleFromFilename($this->file->getClientOriginalName());

        // On auto-update le titre si :
        // - titre vide, OU
        // - titre actuel == baseline auto (donc pas custom), OU
        // - l'utilisateur n'a pas "manuellement édité" le titre
        $current = trim((string) $this->title);

        $canAuto =
            $current === '' ||
            $current === $this->autoTitleBaseline ||
            $this->titleWasManuallyEdited === false;

        // Protection: si l'utilisateur a tapé un titre custom, on ne touche pas.
        if ($canAuto) {
            $this->title = $newSuggested;
            // nouvelle baseline auto devient le titre suggéré du nouveau fichier
            $this->autoTitleBaseline = $newSuggested;
            $this->titleWasManuallyEdited = false;
        }
    }

    /**
     * Quand on change le mode, on nettoie les champs non pertinents
     * pour éviter des états incohérents.
     */
    public function updatedReplaceMode(): void
    {
        if ($this->replace_mode === 'keep') {
            $this->file = null;
            // On garde source_url tel quel (si doc externe) mais pas obligatoire
            return;
        }

        if ($this->replace_mode === 'file') {
            // si on bascule vers fichier, on ignore le lien
            $this->source_url = '';
            return;
        }

        if ($this->replace_mode === 'link') {
            // si on bascule vers lien, on ignore le fichier
            $this->file = null;
            return;
        }
    }

    private function defaultParcourId(): ?int
    {
        // “un seul parcours” => auto via first()
        return Parcour::query()->orderBy('id')->value('id');
    }

    private function resolveSelectedProgramme(): ?Programme
    {
        $ueId = (int) $this->ue_id;
        $ecId = (int) $this->ec_id;

        if ($ecId > 0) {
            return Programme::query()
                ->where('id', $ecId)
                ->where('type', Programme::TYPE_EC)
                ->first();
        }

        if ($ueId > 0) {
            return Programme::query()
                ->where('id', $ueId)
                ->where('type', Programme::TYPE_UE)
                ->first();
        }

        return null;
    }

    public function save(DocumentUploadService $svc)
    {
        try {
            $this->authorize('update', $this->document);

            $parcourId = $this->defaultParcourId();
            if (!$parcourId) {
                $this->alert('error', "Erreur", ['text' => "Aucun parcours trouvé (table parcours vide)."]);
                $this->addError('global', "Aucun parcours trouvé (table parcours vide).");
                return;
            }

            $this->validate([
                'title' => ['required', 'string', 'max:255'],
                'niveau_id' => ['required', 'integer', 'exists:niveaux,id'],

                // UE obligatoire
                'ue_id' => [
                    'required',
                    'integer',
                    Rule::exists('programmes', 'id')->where(function ($q) {
                        $q->where('type', Programme::TYPE_UE)
                          ->whereNull('parent_id')
                          ->where('status', true)
                          ->where('niveau_id', (int) $this->niveau_id);
                    }),
                ],

                // EC optionnel (enfant de l'UE)
                'ec_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('programmes', 'id')->where(function ($q) {
                        $q->where('type', Programme::TYPE_EC)
                          ->where('status', true)
                          ->where('parent_id', (int) $this->ue_id)
                          ->where('niveau_id', (int) $this->niveau_id);
                    }),
                ],

                'is_actif' => ['boolean'],
                'is_archive' => ['boolean'],

                'replace_mode' => ['required', 'in:keep,file,link'],

                'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif'],
                'source_url' => ['nullable', 'url', 'max:2048'],
            ]);

            // Règles dépendantes
            if ($this->replace_mode === 'file') {
                $this->validate([
                    'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif'],
                ]);
            }
            if ($this->replace_mode === 'link') {
                $this->validate([
                    'source_url' => ['required', 'url', 'max:2048'],
                ]);
            }

            $oldFilePath = (string) ($this->document->file_path ?? '');

            // programme_id = EC si choisi sinon UE
            $selectedProgramme = $this->resolveSelectedProgramme();
            if (!$selectedProgramme) {
                $this->alert('error', "Erreur", ['text' => "Programme UE/EC invalide."]);
                $this->addError('global', "Programme UE/EC invalide.");
                return;
            }

            $programmeId = (int) $selectedProgramme->id;
            $semestreId = (int) ($selectedProgramme->semestre_id ?? 0);

            $newData = [
                'title' => $this->title,
                'niveau_id' => (int) $this->niveau_id,

                // auto parcours
                'parcour_id' => (int) $parcourId,

                // programme choisi (EC prioritaire)
                'programme_id' => $programmeId,

                'is_actif' => (bool) $this->is_actif,
                'is_archive' => (bool) $this->is_archive,
            ];

            // semestre auto (si colonne existe)
            if (Schema::hasColumn('documents', 'semestre_id')) {
                $newData['semestre_id'] = $semestreId > 0 ? $semestreId : null;
            }

            // A) Remplacer par FILE
            if ($this->replace_mode === 'file') {
                // IMPORTANT: ta méthode doit accepter TemporaryUploadedFile
                // => handleUploadedFile(UploadedFile|TemporaryUploadedFile, disk, dir)
                $meta = $svc->handleUploadedFile($this->file, 'public', 'documents');

                $path = (string) ($meta['file_path'] ?? '');
                $sizeBytes = (int) ($meta['file_size_bytes'] ?? 0);

                $newData['file_path'] = $path;
                $newData['source_url'] = null;

                // compat colonnes
                if (Schema::hasColumn('documents', 'file_size_bytes')) {
                    $newData['file_size_bytes'] = $sizeBytes ?: null;
                }
                if (Schema::hasColumn('documents', 'file_size')) {
                    $newData['file_size'] = $sizeBytes ?: 0;
                }

                if (Schema::hasColumn('documents', 'original_filename')) {
                    $newData['original_filename'] = $meta['original_filename'] ?? null;
                }
                if (Schema::hasColumn('documents', 'original_extension')) {
                    $newData['original_extension'] = $meta['original_extension'] ?? null;
                }
                if (Schema::hasColumn('documents', 'converted_from')) {
                    $newData['converted_from'] = $meta['converted_from'] ?? null;
                }
                if (Schema::hasColumn('documents', 'converted_at')) {
                    $newData['converted_at'] = $meta['converted_at'] ?? null;
                }
                if (Schema::hasColumn('documents', 'file_type')) {
                    $newData['file_type'] = $meta['file_type'] ?? $this->document->file_type ?? 'other';
                }

                $this->document->update($newData);

                $this->deleteOldLocalFileIfUnused($oldFilePath);

                $this->alert('success', "Succès", ['text' => "Document mis à jour."]);
                return $this->redirectRoute('documents.index', navigate: true);
            }

            // B) Remplacer par LINK
            if ($this->replace_mode === 'link') {
                $url = trim((string) $this->source_url);

                $newData['source_url'] = $url;

                // si file_path est NOT NULL, on met l'URL dedans aussi
                $newData['file_path'] = $url;

                if (Schema::hasColumn('documents', 'file_size_bytes')) {
                    $newData['file_size_bytes'] = null;
                }
                if (Schema::hasColumn('documents', 'file_size')) {
                    $newData['file_size'] = 0;
                }

                if (Schema::hasColumn('documents', 'file_type')) {
                    $newData['file_type'] = 'external';
                }

                $ext = strtolower((string) pathinfo((string) (parse_url($url, PHP_URL_PATH) ?? ''), PATHINFO_EXTENSION));
                if (Schema::hasColumn('documents', 'original_extension')) {
                    $newData['original_extension'] = $ext !== '' ? $ext : null;
                }
                if (Schema::hasColumn('documents', 'converted_from')) {
                    $newData['converted_from'] = null;
                }
                if (Schema::hasColumn('documents', 'converted_at')) {
                    $newData['converted_at'] = null;
                }

                $this->document->update($newData);

                $this->deleteOldLocalFileIfUnused($oldFilePath);

                $this->alert('success', "Succès", ['text' => "Document mis à jour (lien)."]);
                return $this->redirectRoute('documents.index', navigate: true);
            }

            // C) KEEP (meta only)
            $this->document->update($newData);

            $this->alert('success', "Succès", ['text' => "Document mis à jour."]);
            return $this->redirectRoute('documents.index', navigate: true);

        } catch (\Throwable $e) {
            report($e);
            $this->alert('error', "Erreur", ['text' => $e->getMessage()]);
            $this->addError('global', $e->getMessage());
            return;
        }
    }

    private function deleteOldLocalFileIfUnused(string $oldFilePath): void
    {
        $oldFilePath = trim($oldFilePath);
        if ($oldFilePath === '') return;

        if (Str::startsWith($oldFilePath, ['http://', 'https://'])) return;

        $stillUsed = Document::where('file_path', $oldFilePath)
            ->whereKeyNot($this->document->id)
            ->exists();

        if (!$stillUsed) {
            Storage::disk('public')->delete($oldFilePath);
        }
    }

    /**
     * Construit un "titre auto" à partir du document existant.
     * Sert uniquement de baseline pour décider si on peut écraser le title.
     */
    private function guessTitleFromExistingDocument(Document $document): string
    {
        // Priorité: original_filename si dispo (souvent le plus fiable)
        $name = '';

        if (Schema::hasColumn('documents', 'original_filename')) {
            $name = (string) ($document->original_filename ?? '');
        }

        if ($name === '') {
            // Sinon: file_path ou source_url
            $raw = (string) ($document->source_url ?: $document->file_path ?: '');
            $raw = trim($raw);

            if ($raw !== '') {
                if (Str::startsWith($raw, ['http://', 'https://'])) {
                    $path = (string) (parse_url($raw, PHP_URL_PATH) ?? '');
                    $name = $path !== '' ? basename($path) : $raw;
                } else {
                    $name = basename($raw);
                }
            }
        }

        $name = $name !== '' ? $name : (string) ($document->title ?? '');
        return $this->titleFromFilename($name);
    }

    /**
     * Convertit un nom de fichier en titre lisible.
     */
    private function titleFromFilename(string $filename): string
    {
        $base = trim(pathinfo($filename, PATHINFO_FILENAME));
        if ($base === '') return 'document';

        $base = str_replace(['_', '-'], ' ', $base);
        $base = preg_replace('/\s+/', ' ', $base);

        // Option: capitalisation légère
        $base = Str::of($base)->trim()->toString();

        return $base;
    }

    public function render()
    {
        $niveauId = (int) $this->niveau_id;
        $ueId = (int) $this->ue_id;

        $ues = $niveauId > 0
            ? Programme::query()
                ->active()
                ->ues()
                ->where('niveau_id', $niveauId)
                ->orderBy('order')
                ->get()
            : collect();

        $ecs = $ueId > 0
            ? Programme::query()
                ->active()
                ->ecs()
                ->where('parent_id', $ueId)
                ->orderBy('order')
                ->get()
            : collect();

        return view('livewire.documents.document-edit', [
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'ues' => $ues,
            'ecs' => $ecs,
        ]);
    }
}
