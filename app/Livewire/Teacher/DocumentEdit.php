<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
use Livewire\Component;
use App\Models\Document;
use App\Models\Semestre;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentEdit extends Component
{
    use WithFileUploads;

    public Document $document;
    public $title;
    public $currentDateTime;
    public $currentUser;

    #[Rule(['required', 'exists:niveaux,id'])]
    public $niveau_id = '';

    #[Rule(['required', 'exists:parcours,id'])]
    public $parcour_id = '';

    public $is_actif;
    public $newFile;
    public $showNewFile = false;
    public $semestres_selected = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'newFile' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
            'semestres_selected' => 'required|array|min:1'
        ];
    }

    protected function messages()
    {
        return [
            'title.required' => 'Le titre est requis',
            'title.min' => 'Le titre doit contenir au moins 3 caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'niveau_id.required' => 'Le niveau est requis',
            'niveau_id.exists' => 'Niveau invalide',
            'parcour_id.required' => 'Le parcours est requis',
            'parcour_id.exists' => 'Parcours invalide',
            'newFile.file' => 'Fichier invalide',
            'newFile.max' => 'Taille maximale: 10MB',
            'newFile.mimes' => 'Types acceptés: PDF, Word, Excel, PowerPoint, Images',
            'semestres_selected.required' => 'Au moins un semestre actif est requis.',
            'semestres_selected.min' => 'Au moins un semestre actif est requis.',
        ];
    }

    public function mount(Document $document)
    {
        abort_if(
            !Auth::user()?->roles->contains('name', 'teacher') ||
            $document->uploaded_by !== Auth::id(),
            403
        );

        $this->document = $document;
        $this->title = $document->title;
        $this->niveau_id = $document->niveau_id;
        $this->parcour_id = $document->parcour_id;
        $this->is_actif = $document->is_actif;
        $this->currentDateTime = now()->format('Y-m-d H:i:s');
        $this->currentUser = Auth::user()->name;

        // Initialiser les semestres actifs
        if ($this->niveau_id) {
            $this->semestres_selected = $this->semestresActifs->pluck('id')->toArray();
        }
    }

    public function getSemestresActifsProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        return Semestre::where('niveau_id', $this->niveau_id)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function getTeacherNiveauxProperty()
    {
        return Niveau::query()
            ->whereHas('teachers', function($q) {
                $q->where('niveau_user.user_id', Auth::id());
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

        return Parcour::query()
            ->whereHas('teachers', function($q) use ($user) {
                $q->where('parcour_user.user_id', $user->id);
            })
            ->where('status', true)
            ->whereExists(function($query) use ($user) {
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
        // Réinitialiser la sélection
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

        // Rafraîchir l'interface
        $this->dispatch('parcours-updated');
    }

    public function updatedNewFile()
    {
        try {
            $this->validateOnly('newFile');
            $this->showNewFile = true;
        } catch (\Exception $e) {
            $this->newFile = null;
            $this->showNewFile = false;
            session()->flash('error', 'Le type de fichier n\'est pas accepté');
        }
    }

    public function removeNewFile()
    {
        $this->newFile = null;
        $this->showNewFile = false;
    }

    protected function handleFileUpload()
    {
        if (!$this->newFile) {
            return null;
        }

        $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->newFile->getClientOriginalExtension();
        $filePath = $this->newFile->storeAs('documents', $fileName, 'public');

        if (!$filePath) {
            throw new \Exception('Échec du stockage du fichier');
        }

        return [
            'file_path' => $filePath,
            'protected_path' => $filePath,
            'file_type' => $this->newFile->getMimeType(),
            'file_size' => $this->newFile->getSize()
        ];
    }

    public function updateDocument()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Vérifier les semestres
            if (empty($this->semestres_selected)) {
                throw new \Exception('Aucun semestre actif disponible pour ce niveau.');
            }

            // Supprimer l'ancien document
            Document::where('file_path', $this->document->file_path)
                   ->where('uploaded_by', Auth::id())
                   ->delete();

            // Préparer les données de mise à jour
            $updateData = [
                'title' => $this->title,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'is_actif' => $this->is_actif,
                'uploaded_by' => Auth::id(),
                'download_count' => $this->document->download_count,
                'view_count' => $this->document->view_count
            ];

            // Gérer le fichier
            if ($this->newFile) {
                // Supprimer l'ancien fichier
                if (Storage::disk('public')->exists($this->document->file_path)) {
                    Storage::disk('public')->delete($this->document->file_path);
                }

                // Ajouter les données du nouveau fichier
                $fileData = $this->handleFileUpload();
                if ($fileData) {
                    $updateData = array_merge($updateData, $fileData);
                }
            } else {
                // Garder les anciennes données du fichier
                $updateData['file_path'] = $this->document->file_path;
                $updateData['protected_path'] = $this->document->protected_path;
                $updateData['file_type'] = $this->document->file_type;
                $updateData['file_size'] = $this->document->file_size;
            }

            // Créer une entrée pour chaque semestre sélectionné
            foreach ($this->semestres_selected as $semestre_id) {
                Document::create(array_merge($updateData, [
                    'semestre_id' => $semestre_id
                ]));
            }

            DB::commit();
            session()->flash('success', 'Document mis à jour avec succès');
            return redirect()->route('document.teacher');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour document', [
                'error' => $e->getMessage(),
                'document_id' => $this->document->id,
                'user_id' => Auth::id()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.teacher.document-edit', [
            'niveaux' => $this->teacherNiveaux,
            'parcours' => $this->teacherParcours,
            'semestresActifs' => $this->semestresActifs
        ]);
    }
}
