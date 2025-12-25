<?php

namespace App\Livewire\Teacher;

use App\Models\Document;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Programme;
use App\Services\PdfConversionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentEdit extends Component
{
    use WithFileUploads, LivewireAlert;

    public Document $document;

    public string $title = '';

    public ?int $niveau_id = null;

    // UE / EC (UI)
    public ?int $ue_id = null;
    public ?int $ec_id = null;

    // Stocké dans documents
    public ?int $semestre_id = null;
    public ?int $parcour_id = null;
    public ?int $programme_id = null;

    public bool $is_actif = false;

    // Mode fichier
    public string $file_mode = 'local'; // local | external
    public string $external_url = '';

    // Upload remplacement
    public $newFile = null;
    public bool $showNewFile = false;

    public function mount(Document $document): void
    {
        abort_if(
            !Auth::check()
            || !Auth::user()->hasRole('teacher')
            || (int) $document->uploaded_by !== (int) Auth::id(),
            403
        );

        $this->document = $document;

        $this->title       = (string) $document->title;
        $this->niveau_id   = $document->niveau_id ? (int) $document->niveau_id : null;
        $this->semestre_id = $document->semestre_id ? (int) $document->semestre_id : null;
        $this->parcour_id  = $document->parcour_id ? (int) $document->parcour_id : null;
        $this->programme_id = $document->programme_id ? (int) $document->programme_id : null;

        $this->is_actif = (bool) $document->is_actif;

        // Détecter externe
        $oldPath = (string) $document->file_path;
        $isExternal = Str::startsWith($oldPath, ['http://','https://'])
            || (string) $document->file_type === 'link'
            || (string) $document->original_extension === 'url';

        $this->file_mode = $isExternal ? 'external' : 'local';
        $this->external_url = $isExternal ? $oldPath : '';

        /**
         * Préchargement UE/EC fiable via programme_id :
         * - programme_id = EC => UE = parent_id, EC = programme_id
         * - programme_id = UE => UE = programme_id, EC = null
         */
        if ($this->programme_id) {
            $p = Programme::query()
                ->select('id','type','parent_id','niveau_id')
                ->find($this->programme_id);

            if ($p && $this->niveau_id && (int)$p->niveau_id === (int)$this->niveau_id) {
                if ($p->type === 'EC' && $p->parent_id) {
                    $this->ue_id = (int) $p->parent_id;
                    $this->ec_id = (int) $p->id;
                } elseif ($p->type === 'UE') {
                    $this->ue_id = (int) $p->id;
                    $this->ec_id = null;
                }
            }
        }

        /**
         * Fallback (anciens documents sans programme_id) :
         * on devine une UE à partir niveau + semestre + parcour
         */
        if (!$this->ue_id && $this->niveau_id) {
            $q = Programme::query()
                ->where('type', 'UE')
                ->where('status', true)
                ->where('niveau_id', (int) $this->niveau_id);

            if ($this->semestre_id) {
                $q->where('semestre_id', (int) $this->semestre_id);
            }
            if ($this->parcour_id) {
                $q->where('parcour_id', (int) $this->parcour_id);
            }

            $guessUeId = (int) $q->orderBy('order')->orderBy('id')->value('id');
            if ($guessUeId > 0) {
                $this->ue_id = $guessUeId;
                $this->ec_id = null;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'title'     => 'required|string|min:3|max:255',
            'niveau_id' => 'required|integer|exists:niveaux,id',

            'ue_id' => 'required|integer|exists:programmes,id',
            'ec_id' => 'nullable|integer|exists:programmes,id',

            'is_actif' => 'boolean',

            'file_mode'    => 'required|in:local,external',
            'external_url' => 'nullable|url|max:2048',

            'newFile' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'niveau_id.required' => 'Le niveau est requis.',
            'ue_id.required' => 'Veuillez choisir une UE.',
            'external_url.url' => 'Lien externe invalide.',
            'newFile.max' => 'Taille maximale : 10MB.',
            'newFile.mimes' => 'Types acceptés : PDF, Word, Excel, PowerPoint, Images.',
        ];
    }

    public function updatedFileMode(): void
    {
        if ($this->file_mode === 'external') {
            $this->newFile = null;
            $this->showNewFile = false;
        } else {
            $this->external_url = '';
        }
    }

    public function updatedNiveauId(): void
    {
        $this->ue_id = null;
        $this->ec_id = null;

        // recalculé à l’enregistrement
        $this->semestre_id = null;
        $this->parcour_id = null;
        $this->programme_id = null;
    }

    public function updatedUeId(): void
    {
        $this->ec_id = null;
    }

    public function updatedNewFile(): void
    {
        if ($this->file_mode !== 'local') {
            $this->newFile = null;
            $this->showNewFile = false;
            return;
        }

        try {
            $this->validateOnly('newFile');
            $this->showNewFile = true;
        } catch (\Throwable $e) {
            $this->newFile = null;
            $this->showNewFile = false;

            $this->alert('error', 'Fichier non accepté', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    public function removeNewFile(): void
    {
        $this->newFile = null;
        $this->showNewFile = false;
    }

    public function getTeacherNiveauxProperty()
    {
        return Niveau::query()
            ->where('status', true)
            ->orderBy('id')
            ->get();
    }

    public function getUesProperty()
    {
        if (!$this->niveau_id) return collect();

        return Programme::query()
            ->active()
            ->ues()
            ->where('niveau_id', (int) $this->niveau_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    public function getEcsProperty()
    {
        if (!$this->ue_id) return collect();

        return Programme::query()
            ->active()
            ->ecs()
            ->where('parent_id', (int) $this->ue_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    private function conversionService(): PdfConversionService
    {
        return app(PdfConversionService::class);
    }

    private function renameExistingFile(string $oldFilePath, string $newTitle): ?string
    {
        if (!Storage::disk('public')->exists($oldFilePath)) {
            return null;
        }

        $oldExtension = strtolower(pathinfo($oldFilePath, PATHINFO_EXTENSION));
        $newFileName = time() . '_' . Str::slug($newTitle) . '_' . Str::random(8) . '.' . $oldExtension;
        $newFilePath = 'documents/' . $newFileName;

        $oldAbs = Storage::disk('public')->path($oldFilePath);
        $newAbs = Storage::disk('public')->path($newFilePath);

        try {
            if (@rename($oldAbs, $newAbs)) {
                return $newFilePath;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    private function handleFileUpload(): array
    {
        $originalExtension = strtolower($this->newFile->getClientOriginalExtension());
        $needsConversion = in_array($originalExtension, ['doc', 'docx', 'ppt', 'pptx'], true);

        if ($needsConversion) {
            return $this->convertToPdf($this->newFile, $this->title, $originalExtension);
        }

        $fileName = time() . '_' . Str::slug($this->title) . '_' . Str::random(8) . '.' . $originalExtension;
        $filePath = $this->newFile->storeAs('documents', $fileName, 'public');

        return [
            'file_path' => $filePath,
            'protected_path' => $filePath,
            'file_type' => $originalExtension,
            'file_size' => Storage::disk('public')->size($filePath),
            'original_filename' => $this->newFile->getClientOriginalName(),
            'original_extension' => $originalExtension,
            'converted_from' => null,
            'converted_at' => null,
        ];
    }

    private function convertToPdf($file, string $title, string $originalExtension): array
    {
        $tempPath = $file->storeAs('temp', Str::random(10) . '.' . $originalExtension);
        $absoluteTempPath = storage_path('app/' . $tempPath);

        try {
            $outputDir = storage_path('app/public/documents');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $pdfFileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.pdf';
            $this->conversionService()->convertToPdf($absoluteTempPath, $outputDir, $pdfFileName);

            $finalPath = 'documents/' . $pdfFileName;

            return [
                'file_path' => $finalPath,
                'protected_path' => $finalPath,
                'file_type' => 'pdf',
                'file_size' => Storage::disk('public')->size($finalPath),
                'original_filename' => $file->getClientOriginalName(),
                'original_extension' => $originalExtension,
                'converted_from' => $originalExtension,
                'converted_at' => now(),
            ];
        } finally {
            Storage::delete($tempPath);
        }
    }

    /**
     * Valide UE/EC + retourne programme_id + semestre_id + parcour_id
     */
    private function validateProgrammeSelection(): array
    {
        $ue = Programme::query()
            ->select('id', 'type', 'niveau_id', 'semestre_id', 'parcour_id')
            ->findOrFail((int) $this->ue_id);

        if ($ue->type !== 'UE') {
            throw new \Exception("Le programme choisi comme UE n'est pas une UE.");
        }
        if ((int) $ue->niveau_id !== (int) $this->niveau_id) {
            throw new \Exception("Cette UE n'appartient pas au niveau sélectionné.");
        }

        $programmeId = (int) ($this->ec_id ?: $this->ue_id);

        $programme = Programme::query()
            ->select('id', 'type', 'parent_id', 'niveau_id', 'semestre_id', 'parcour_id')
            ->findOrFail($programmeId);

        if ((int) $programme->niveau_id !== (int) $this->niveau_id) {
            throw new \Exception("Le programme sélectionné n'appartient pas au niveau choisi.");
        }

        if ($programme->type === 'EC') {
            if ((int) $programme->parent_id !== (int) $this->ue_id) {
                throw new \Exception("L'EC sélectionné n'appartient pas à l'UE choisie.");
            }
        } elseif ($programme->type !== 'UE') {
            throw new \Exception("Type de programme invalide.");
        }

        $semestreId = (int) ($programme->semestre_id ?: $ue->semestre_id);
        if (!$semestreId) {
            throw new \Exception("Le programme sélectionné n'a pas de semestre_id.");
        }

        $parcourId = (int) ($ue->parcour_id ?: $programme->parcour_id);
        if (!$parcourId) {
            throw new \Exception("Le programme sélectionné n'a pas de parcour_id.");
        }

        return [
            'programme_id' => $programmeId,
            'semestre_id'  => $semestreId,
            'parcour_id'   => $parcourId,
        ];
    }

    public function updateDocument()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $programmeData = $this->validateProgrammeSelection();

            $this->programme_id = (int) $programmeData['programme_id'];
            $this->semestre_id  = (int) $programmeData['semestre_id'];
            $this->parcour_id   = (int) $programmeData['parcour_id'];

            $oldFilePath = (string) $this->document->file_path;
            $oldIsExternal = Str::startsWith($oldFilePath, ['http://','https://'])
                || (string) $this->document->file_type === 'link'
                || (string) $this->document->original_extension === 'url';

            $updateData = [
                'title'       => $this->title,
                'niveau_id'   => (int) $this->niveau_id,
                'parcour_id'  => (int) $this->parcour_id,
                'semestre_id' => (int) $this->semestre_id,
                'programme_id'=> (int) $this->programme_id,
                'is_actif'    => (bool) $this->is_actif,
                'updated_at'  => now(),
            ];

            // MODE EXTERNE
            if ($this->file_mode === 'external') {
                if (!filled($this->external_url)) {
                    throw new \Exception("Veuillez renseigner le lien externe.");
                }

                if (!$oldIsExternal && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                $updateData = array_merge($updateData, [
                    'file_path' => $this->external_url,
                    'protected_path' => null,
                    'file_type' => 'link',
                    'file_size' => 0,
                    'original_filename' => $this->title,
                    'original_extension' => 'url',
                    'converted_from' => null,
                    'converted_at' => null,
                ]);
            }

            // MODE LOCAL
            if ($this->file_mode === 'local') {
                if ($oldIsExternal && !$this->newFile) {
                    throw new \Exception("Vous devez choisir un fichier pour remplacer le lien externe.");
                }

                if ($this->newFile) {
                    $fileData = $this->handleFileUpload();
                    $updateData = array_merge($updateData, $fileData);

                    if (!$oldIsExternal && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                } elseif (!$oldIsExternal && $this->title !== $this->document->title && $oldFilePath) {
                    $newPath = $this->renameExistingFile($oldFilePath, $this->title);
                    if ($newPath) {
                        $updateData['file_path'] = $newPath;
                        $updateData['protected_path'] = $newPath;
                    }
                }
            }

            Document::where('id', $this->document->id)->update($updateData);

            DB::commit();

            $this->alert('success', 'Document mis à jour', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return $this->redirect(route('document.teacher'), navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();

            $this->alert('error', 'Erreur : ' . $e->getMessage(), [
                'position' => 'center',
                'toast' => false,
                'timer' => 7000,
            ]);

            return null;
        }
    }

    public function render()
    {
        return view('livewire.teacher.document-edit', [
            'niveaux' => $this->teacherNiveaux,
            'ues' => $this->ues,
            'ecs' => $this->ecs,
        ]);
    }
}
