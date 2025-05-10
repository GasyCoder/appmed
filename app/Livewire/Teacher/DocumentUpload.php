<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Document;
use App\Models\Semestre;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DocumentUpload extends Component
{
    use WithFileUploads;
    use WithPagination;
    use LivewireAlert;

    // Règles pour les fichiers
    #[Rule(['array'])]
    public $file = [];
    public $file_status = [];
    public $titles = [];

    // Règles pour les sélections
    public $niveau_id = '';
    public $parcour_id = '';
    public $is_actif = true;
    public $semestres_selected = [];
    const MAX_FILES = 6;

    public $isUploading = false;
    public $uploadProgress = 0;
    public $successMessage = '';
    public $errorMessage = '';

    public function mount()
    {
        $this->file = [];
        $this->titles = [];
        $this->file_status = [];

        if ($this->niveau_id) {
            $this->updatedNiveauId();
        }
    }

    // Méthodes pour la gestion des fichiers
    public function updatedFile()
    {
        if (!empty($this->file)) {
            $this->validate([
                'file' => 'array|max:' . self::MAX_FILES,
                'file.*' => 'file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png'
            ]);

            foreach ($this->file as $index => $file) {
                if (!isset($this->titles[$index])) {
                    $this->titles[$index] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                }
                if (!isset($this->file_status[$index])) {
                    $this->file_status[$index] = true;
                }
            }
        }
    }

    public function removeFile($index)
    {
        if (isset($this->file[$index])) {
            unset($this->file[$index]);
            $this->file = array_values($this->file);

            if (isset($this->titles[$index])) {
                unset($this->titles[$index]);
                $this->titles = array_values($this->titles);
            }

            if (isset($this->file_status[$index])) {
                unset($this->file_status[$index]);
                $this->file_status = array_values($this->file_status);
            }
        }
    }

    // Propriétés calculées
    public function getSemestresActifsProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        $semestres = Semestre::where('niveau_id', $this->niveau_id)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();

        // Mettre à jour automatiquement les semestres sélectionnés
        $this->semestres_selected = $semestres->pluck('id')->toArray();

        return $semestres;
    }

    public function getTeacherNiveauxProperty()
    {
        return Niveau::whereHas('teachers', function($q) {
            $q->where('users.id', Auth::id());
        })
        ->where('status', true)
        ->orderBy('name')
        ->get();
    }

    public function getTeacherParcoursProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        $user = Auth::user();

        return Parcour::whereHas('teachers', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->where('status', true)
            ->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                      ->from('niveau_user')
                      ->where('user_id', $user->id)
                      ->where('niveau_id', $this->niveau_id);
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedNiveauId()
    {
        $this->parcour_id = '';
        $this->semestres_selected = [];

        // Récupérer les parcours disponibles
        $availableParcours = $this->teacherParcours;

        // Sélectionner le premier parcours si disponible
        if ($availableParcours->isNotEmpty()) {
            $this->parcour_id = $availableParcours->first()->id;
        }

        // Mettre à jour les semestres
        if ($this->niveau_id) {
            $this->semestres_selected = $this->semestresActifs->pluck('id')->toArray();
        }

        // Émettre un événement pour rafraîchir l'interface
        $this->dispatch('parcours-updated');
    }

    // Méthode principale d'upload - Optimisée pour éviter le rechargement
    public function uploadDocument()
    {
        if (count($this->file) > self::MAX_FILES) {
            $this->alert('error', 'Vous ne pouvez pas téléverser plus de ' . self::MAX_FILES . ' fichiers à la fois.');
            return;
        }

        $this->validate([
            'file' => 'required|array|max:' . self::MAX_FILES,
            'file.*' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png'
            ],
            'titles.*' => 'required|string|min:3|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
            'semestres_selected' => 'required|array|min:1',
        ]);

        $this->isUploading = true;
        $this->uploadProgress = 0;
        $this->successMessage = '';
        $this->errorMessage = '';

        try {
            DB::beginTransaction();

            if (empty($this->semestres_selected)) {
                throw new \Exception('Aucun semestre actif disponible pour ce niveau.');
            }

            $totalFiles = count($this->file);
            $filesProcessed = 0;

            foreach ($this->file as $index => $uploadedFile) {
                if (empty($this->titles[$index])) {
                    throw new \Exception('Titre requis pour tous les fichiers');
                }

                $filePath = $this->storeDocument($uploadedFile, $this->titles[$index]);
                $this->createDocument($this->titles[$index], $uploadedFile, $filePath, $index);

                $filesProcessed++;
                $this->uploadProgress = intval(($filesProcessed / $totalFiles) * 100);
                $this->dispatch('upload-progress-updated', progress: $this->uploadProgress);
            }

            DB::commit();

            $this->alert('success', count($this->file) . ' document(s) téléversé(s) avec succès', [
                'position' => 'top-end', // Position changé pour un meilleur UX
                'timer' => 4000, // Timer réduit pour une meilleure réactivité
                'toast' => true, // Style compact
                'timerProgressBar' => true,
                'showConfirmButton' => false,
                'width' => '400px', // Largeur ajustée pour une meilleure présentation
                'padding' => '1.5em', // Padding ajusté pour une meilleure lisibilité
                'icon' => 'success', // Ajout d'une icône de succès
                'background' => '#f0f9ff', // Couleur de fond personnalisée pour une meilleure esthétique
                'customClass' => [
                    'popup' => 'custom-alert',
                    'title' => 'text-lg font-semibold mb-2',
                    'text' => 'text-sm'
                ],
                'showClass' => [
                    'popup' => 'animate__animated animate__fadeInDown' // Animation d'apparition
                ],
                'hideClass' => [
                    'popup' => 'animate__animated animate__fadeOutUp' // Animation de disparition
                ]
            ]);

            return redirect()->route('document.teacher');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload error:', [
                'error' => $e->getMessage(),
                'user' => Auth::id()
            ]);
            $this->alert('error', $e->getMessage(), [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);
        } finally {
            $this->isUploading = false;
            $this->uploadProgress = 100;
        }
    }

    // Méthodes privées
    private function storeDocument($file, $title)
    {
        $fileName = time() . '_' . Str::slug($title) . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        if (!$filePath) {
            throw new \Exception('Échec du stockage du fichier.');
        }

        return $filePath;
    }

    private function createDocument($title, $file, $filePath, $index)
    {
        foreach ($this->semestres_selected as $semestre_id) {
            Document::create([
                'title' => $title,
                'file_path' => $filePath,
                'protected_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'semestre_id' => $semestre_id,
                'uploaded_by' => Auth::id(),
                'is_actif' => $this->file_status[$index] ?? false,
                'download_count' => 0,
                'view_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function resetErrorMessage()
    {
        $this->errorMessage = '';
    }

    public function resetSuccessMessage()
    {
        $this->successMessage = '';
    }

    public function render()
    {
        return view('livewire.teacher.document-upload');
    }
}
