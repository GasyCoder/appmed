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
    public string $niveau_id = '';
    public ?int $parcour_id = null; // auto (un seul parcours)
    public string $ue_id = '';
    public string $ec_id = '';

    public bool $is_actif = false;

    public $newFile = null;
    public bool $showNewFile = false;

    public function mount(Document $document): void
    {
        abort_if(
            !Auth::check() ||
            !Auth::user()->hasRole('teacher') ||
            (int) $document->uploaded_by !== (int) Auth::id(),
            403
        );

        $this->document = $document;

        $this->title = (string) $document->title;
        $this->niveau_id = (string) $document->niveau_id;
        $this->parcour_id = $document->parcour_id ?: Parcour::where('status', true)->orderBy('name')->value('id');
        $this->is_actif = (bool) $document->is_actif;

        // Précharger UE/EC depuis programme_id
        if ($document->programme_id) {
            $p = Programme::query()
                ->select('id', 'type', 'parent_id')
                ->find($document->programme_id);

            if ($p) {
                if ($p->type === 'EC' && $p->parent_id) {
                    $this->ue_id = (string) $p->parent_id;
                    $this->ec_id = (string) $p->id;
                } else {
                    $this->ue_id = (string) $p->id;
                    $this->ec_id = '';
                }
            }
        }
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'niveau_id' => 'required|exists:niveaux,id',

            // UE obligatoire, EC optionnel
            'ue_id' => 'required|exists:programmes,id',
            'ec_id' => 'nullable|exists:programmes,id',

            'is_actif' => 'boolean',
            'newFile' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'title.min' => 'Le titre doit contenir au moins 3 caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',

            'niveau_id.required' => 'Le niveau est requis.',
            'niveau_id.exists' => 'Niveau invalide.',

            'ue_id.required' => 'Veuillez choisir une UE.',
            'ue_id.exists' => 'UE invalide.',

            'ec_id.exists' => 'EC invalide.',

            'newFile.max' => 'Taille maximale : 10MB.',
            'newFile.mimes' => 'Types acceptés : PDF, Word, Excel, PowerPoint, Images.',
        ];
    }

    public function getTeacherNiveauxProperty()
    {
        return Niveau::query()
            ->whereHas('teachers', fn ($q) => $q->where('niveau_user.user_id', Auth::id()))
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function getUesProperty()
    {
        if (!$this->niveau_id) return collect();

        $baseQuery = Programme::query()
            ->where('type', 'UE')
            ->where('niveau_id', (int) $this->niveau_id)
            ->where('status', true)
            ->orderBy('order');

        // Si vos UEs ont parcour_id et que vous voulez filtrer : on tente, puis fallback si vide.
        $withParcour = (clone $baseQuery);
        if ($this->parcour_id) {
            $withParcour->where('parcour_id', (int) $this->parcour_id);
        }

        $ues = $withParcour->get();
        if ($ues->isEmpty()) {
            $ues = $baseQuery->get();
        }

        return $ues;
    }

    public function getEcsProperty()
    {
        if (!$this->ue_id) return collect();

        return Programme::query()
            ->where('type', 'EC')
            ->where('parent_id', (int) $this->ue_id)
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }

    public function updatedNiveauId(): void
    {
        // Reset UE/EC quand on change de niveau
        $this->ue_id = '';
        $this->ec_id = '';
    }

    public function updatedUeId(): void
    {
        // Reset EC quand UE change
        $this->ec_id = '';
    }

    public function updatedNewFile(): void
    {
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
            // ignore -> return null
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
            'file_type' => $this->newFile->getMimeType(),
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
                'file_type' => 'application/pdf',
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

    private function validateProgrammeSelection(): array
    {
        // UE doit être une UE et correspondre au niveau sélectionné
        $ue = Programme::query()
            ->select('id', 'type', 'niveau_id', 'parcour_id', 'semestre_id')
            ->findOrFail((int) $this->ue_id);

        if ($ue->type !== 'UE') {
            throw new \Exception("Le programme choisi comme UE n'est pas une UE.");
        }

        if ((int) $ue->niveau_id !== (int) $this->niveau_id) {
            throw new \Exception("Cette UE n'appartient pas au niveau sélectionné.");
        }

        // Programme final = EC si choisi sinon UE
        $programmeId = (int) ($this->ec_id ?: $this->ue_id);

        $programme = Programme::query()
            ->select('id', 'type', 'parent_id', 'niveau_id', 'semestre_id')
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

        // semestre_id obligatoire côté documents
        $semestreId = $programme->semestre_id ?: $ue->semestre_id;
        if (!$semestreId) {
            throw new \Exception("Le programme sélectionné n'a pas de semestre_id. Corrigez la table programmes.");
        }

        return [
            'programme_id' => (int) $programmeId,
            'semestre_id' => (int) $semestreId,
        ];
    }

    public function updateDocument()
    {
        $this->validate();

        // Parcours auto si absent (plateforme mono-parcours)
        if (!$this->parcour_id) {
            $this->parcour_id = Parcour::where('status', true)->orderBy('name')->value('id');
        }

        try {
            DB::beginTransaction();

            // Validation UE/EC + récupération programme_id / semestre_id
            $programmeData = $this->validateProgrammeSelection();

            $oldFilePath = $this->document->file_path;

            $updateData = [
                'title' => $this->title,
                'niveau_id' => (int) $this->niveau_id,
                'parcour_id' => (int) $this->parcour_id,
                'programme_id' => $programmeData['programme_id'],
                'semestre_id' => $programmeData['semestre_id'],
                'is_actif' => (bool) $this->is_actif,
                'updated_at' => now(),
            ];

            // Nouveau fichier => upload/convert + suppression ancien fichier
            if ($this->newFile) {
                $fileData = $this->handleFileUpload();
                $updateData = array_merge($updateData, $fileData);

                if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            // Sinon, si juste titre modifié => rename physique
            elseif ($this->title !== $this->document->title && $oldFilePath) {
                $newPath = $this->renameExistingFile($oldFilePath, $this->title);
                if ($newPath) {
                    $updateData['file_path'] = $newPath;
                    $updateData['protected_path'] = $newPath;
                }
            }

            Document::where('id', $this->document->id)->update($updateData);

            DB::commit();

            $this->alert('success', 'Document mis à jour', [
                'position' => 'top-end',
                'timer' => 3500,
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
