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

class DocumentUpload extends Component
{
    use WithFileUploads, LivewireAlert;

    public array $files = [];
    public array $titles = [];
    public array $statuses = [];

    public string $niveau_id = '';
    public ?int $parcour_id = null; // auto (un seul parcours)
    public string $ue_id = '';
    public string $ec_id = '';

    public const MAX_FILES = 6;
    public const MAX_FILE_SIZE = 10240; // 10MB

    public function mount(): void
    {
        // Parcours automatique (un seul parcours actif)
        $this->parcour_id = Parcour::where('status', true)->orderBy('name')->value('id');

        // Niveau par défaut
        $firstNiveauId = Niveau::where('status', true)->orderBy('name')->value('id');
        if ($firstNiveauId) {
            $this->niveau_id = (string) $firstNiveauId;
        }
    }

    public function updatedFiles(): void
    {
        $this->validate([
            'files' => 'array|max:' . self::MAX_FILES,
            'files.*' => 'file|max:' . self::MAX_FILE_SIZE . '|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
        ]);

        foreach ($this->files as $index => $file) {
            $this->titles[$index] = $this->titles[$index]
                ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $this->statuses[$index] = $this->statuses[$index] ?? true;
        }
    }

    public function clearFiles(): void
    {
        $this->files = [];
        $this->titles = [];
        $this->statuses = [];
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index], $this->titles[$index], $this->statuses[$index]);

        $this->files = array_values($this->files);
        $this->titles = array_values($this->titles);
        $this->statuses = array_values($this->statuses);
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

    public function uploadDocuments()
    {
        if (!$this->parcour_id) {
            $this->alert('error', 'Configuration manquante', [
                'text' => "Aucun parcours actif n'est configuré.",
                'toast' => false,
                'position' => 'center',
            ]);
            return;
        }

        $this->validate([
            'files' => 'required|array|min:1|max:' . self::MAX_FILES,
            'files.*' => 'required|file|max:' . self::MAX_FILE_SIZE . '|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png',
            'titles.*' => 'required|string|min:3|max:255',
            'statuses.*' => 'nullable|boolean',
            'niveau_id' => 'required|exists:niveaux,id',
            'ue_id' => 'required|exists:programmes,id',
            'ec_id' => 'nullable|exists:programmes,id',
        ]);

        // Programme choisi (EC si présent sinon UE)
        $programmeId = $this->ec_id ?: $this->ue_id;

        // IMPORTANT : semestre_id est obligatoire dans votre table documents
        $programme = Programme::query()
            ->select('id', 'semestre_id')
            ->findOrFail((int) $programmeId);

        if (!$programme->semestre_id) {
            $this->alert('error', 'Donnée incomplète', [
                'text' => "Le programme sélectionné n'a pas de semestre_id. Corrigez la table programmes.",
                'toast' => false,
                'position' => 'center',
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $uploaded = 0;
            $converted = 0;

            foreach ($this->files as $index => $file) {
                $result = $this->processFile(
                    $file,
                    $this->titles[$index] ?? 'Document',
                    $index,
                    (int) $programme->id,
                    (int) $programme->semestre_id
                );

                if ($result['success']) {
                    $uploaded++;
                    if ($result['converted']) $converted++;
                }
            }

            DB::commit();

            $message = "{$uploaded} document(s) uploadé(s)";
            if ($converted > 0) $message .= " ({$converted} converti(s) en PDF)";

            $this->alert('success', 'Succès', [
                'position' => 'top-end',
                'timer' => 3500,
                'toast' => true,
                'text' => $message,
            ]);

            return redirect()->route('document.teacher');
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'toast' => false,
                'text' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function processFile($file, string $title, int $index, int $programmeId, int $semestreId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $needsConversion = in_array($extension, ['doc', 'docx', 'ppt', 'pptx'], true);

        if ($needsConversion) {
            $result = $this->convertToPdf($file, $title);
        } else {
            $fileName = time() . '_' . Str::slug($title) . '_' . Str::random(6) . '.' . $extension;
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $result = [
                'path' => $filePath,
                'converted' => false,
                'mime' => $file->getMimeType(),
            ];
        }

        $absolutePath = storage_path('app/public/' . $result['path']);

        Document::create([
            'title' => $title,
            'file_path' => $result['path'],
            'file_type' => $result['mime'],
            'file_size' => file_exists($absolutePath) ? filesize($absolutePath) : 0,

            'original_filename' => $file->getClientOriginalName(),
            'original_extension' => $extension,
            'converted_from' => $result['converted'] ? $extension : null,
            'converted_at' => $result['converted'] ? now() : null,

            'niveau_id' => (int) $this->niveau_id,
            'parcour_id' => (int) $this->parcour_id,
            'semestre_id' => $semestreId,
            'programme_id' => $programmeId,

            'uploaded_by' => Auth::id(),
            'is_actif' => (bool) ($this->statuses[$index] ?? false),
        ]);

        return [
            'success' => true,
            'converted' => (bool) $result['converted'],
        ];
    }

    private function convertToPdf($file, string $title): array
    {
        $conversionService = app(\App\Services\PdfConversionService::class);

        $ext = strtolower($file->getClientOriginalExtension());

        // 1) Stocker TOUJOURS le fichier temporaire sur le disk "local"
        \Illuminate\Support\Facades\Storage::disk('local')->makeDirectory('temp');

        $tmpName  = \Illuminate\Support\Str::random(16) . '.' . $ext;
        $tempPath = $file->storeAs('temp', $tmpName, 'local'); // => temp/xxxx.docx

        $absoluteTempPath = \Illuminate\Support\Facades\Storage::disk('local')->path($tempPath);

        // Sécurité: vérifier que le fichier existe réellement
        clearstatcache(true, $absoluteTempPath);
        if (!is_file($absoluteTempPath)) {
            throw new \RuntimeException("Le fichier temporaire n'a pas été créé: {$absoluteTempPath}");
        }

        // 2) Dossier de sortie sur le disk "public"
        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('documents');
        $outputDir = \Illuminate\Support\Facades\Storage::disk('public')->path('documents');

        $pdfFileName = time() . '_' . \Illuminate\Support\Str::slug($title) . '_' . \Illuminate\Support\Str::random(6) . '.pdf';

        try {
            $conversionService->convertToPdf($absoluteTempPath, $outputDir, $pdfFileName);

            return [
                'path'      => 'documents/' . $pdfFileName,
                'converted' => true,
                'mime'      => 'application/pdf',
            ];
        } finally {
            // IMPORTANT: supprimer sur le MÊME disk (local)
            \Illuminate\Support\Facades\Storage::disk('local')->delete($tempPath);
        }
    }


    public function render()
    {
        $niveaux = Niveau::where('status', true)->orderBy('name')->get();

        $ues = collect();
        if ($this->niveau_id) {
            $baseQuery = Programme::where('type', 'UE')
                ->where('niveau_id', (int) $this->niveau_id)
                ->where('status', true)
                ->orderBy('order');

            // Filtre parcours si vos UEs ont bien parcour_id
            $withParcour = (clone $baseQuery);
            if ($this->parcour_id) {
                $withParcour->where('parcour_id', (int) $this->parcour_id);
            }

            $ues = $withParcour->get();

            // Fallback si parcour_id est vide côté programmes (cas fréquent)
            if ($ues->isEmpty()) {
                $ues = $baseQuery->get();
            }
        }

        $ecs = collect();
        if ($this->ue_id) {
            $ecs = Programme::where('type', 'EC')
                ->where('parent_id', (int) $this->ue_id)
                ->where('status', true)
                ->orderBy('order')
                ->get();
        }

        return view('livewire.teacher.document-upload', [
            'niveaux' => $niveaux,
            'ues' => $ues,
            'ecs' => $ecs,
        ]);
    }
}
