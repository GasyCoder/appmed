<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
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

    // Règles pour les fichiers
    #[Rule(['array'])]
    #[Rule(['file', 'max:10240', 'mimes:pdf,doc,docx,dotx,dot,ppt,pptx,xls,xlsx,jpeg,jpg,png'], as: 'file.*')]
    public $file = [];
    public $file_status = [];
    public $titles = [];

    // Règles pour les sélections
    #[Rule(['required', 'exists:niveaux,id'])]
    public $niveau_id = '';

    #[Rule(['required', 'exists:parcours,id'])]
    public $parcour_id = '';

    public $is_actif = true;
    public $semestres_selected = [];

    protected $messages = [
        'file.required' => 'Veuillez sélectionner au moins un fichier.',
        'file.*.file' => 'Chaque élément doit être un fichier valide.',
        'file.*.max' => 'La taille maximale autorisée pour chaque fichier est de 10MB.',
        'file.*.mimes' => 'Les types de fichiers acceptés sont : PDF, Word, Excel, PowerPoint, Images.',
        'titles.*.required' => 'Un titre est requis pour chaque fichier.',
        'titles.*.min' => 'Chaque titre doit contenir au moins 3 caractères.',
        'titles.*.max' => 'Chaque titre ne peut pas dépasser 255 caractères.',
        'niveau_id.required' => 'Le niveau est requis.',
        'niveau_id.exists' => 'Le niveau sélectionné est invalide.',
        'parcour_id.required' => 'Le parcours est requis.',
        'parcour_id.exists' => 'Le parcours sélectionné est invalide.',
        'semestres_selected.required' => 'Au moins un semestre actif est requis.',
        'semestres_selected.min' => 'Au moins un semestre actif est requis.',
    ];

    // Méthodes pour la gestion des fichiers
    public function updatedFile()
    {
        if (!empty($this->file)) {
            $this->titles = array_fill(0, count($this->file), '');
            $this->file_status = array_fill(0, count($this->file), true);
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
            $q->where('users.id', Auth::id())
                ->where('status', true);
        })->orderBy('name')->get();
    }

    public function getTeacherParcoursProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }
    
        return Parcour::whereHas('teachers', function($query) {
            $query->where('user_id', Auth::id())
                  ->where('status', true);
        })
        ->when($this->niveau_id, function($query) {
            $query->whereHas('teachers', function($q) {
                $q->where('user_id', Auth::id())
                  ->whereHas('teacherNiveaux', function($n) {
                      $n->where('niveau_id', $this->niveau_id);
                  });
            });
        })
        ->orderBy('name')
        ->get();
    }

    // Hooks Livewire
    public function mount()
    {
        if (!Auth::user() || !Auth::user()->roles->contains('name', 'teacher')) {
            return redirect()->route('dashboard');
        }
    }

    public function updatedNiveauId()
    {
        // Reset selections
        $this->parcour_id = $this->teacherParcours->first()->id ?? '';

        // Récupérer automatiquement les semestres actifs
        if ($this->niveau_id) {
            $this->semestres_selected = $this->semestresActifs->pluck('id')->toArray();
        } else {
            $this->semestres_selected = [];
        }
    }

    // Méthode principale d'upload
    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|array',
            'file.*' => 'file|max:10240|mimes:pdf,doc,docx,dotx,doc,dot,ppt,pptx,xls,xlsx,jpeg,jpg,png',
            'titles.*' => 'required|string|min:3|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
            'semestres_selected' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            if (empty($this->semestres_selected)) {
                throw new \Exception('Aucun semestre actif disponible pour ce niveau.');
            }

            foreach ($this->file as $index => $uploadedFile) {
                if (empty($this->titles[$index])) {
                    throw new \Exception('Titre requis pour tous les fichiers');
                }

                $filePath = $this->storeDocument($uploadedFile, $this->titles[$index]);
                $this->createDocument($this->titles[$index], $uploadedFile, $filePath, $index);
            }

            DB::commit();
            session()->flash('success', count($this->file) . ' document(s) téléversé(s) avec succès');
            return redirect()->route('document.upload');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload error:', [
                'error' => $e->getMessage(),
                'user' => Auth::id()
            ]);
            session()->flash('error', $e->getMessage());
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

    public function render()
    {
        return view('livewire.teacher.document-upload');
    }
}
