<?php

namespace App\Livewire;

use App\Models\Programme;
use App\Models\Semestre;
use App\Models\Niveau;
use App\Models\Parcour;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditProgramme extends Component
{
    use LivewireAlert; // Ajouter ce trait

    public $showModal = false;
    public $programmeId;
    public $programme;
    
    // Champs du formulaire
    public $type;
    public $code;
    public $name;
    public $credits;
    public $coefficient;
    public $semestre_id;
    public $niveau_id;
    public $parcour_id;
    public $parent_id;
    public $status = true;

    #[On('openEditModal')]
    public function openModal($programmeId)
    {
        $this->resetForm();
        $this->resetValidation();
        
        $this->programmeId = (int) $programmeId;
        $this->programme = Programme::with('parent')->findOrFail($this->programmeId);
        
        // Remplir les champs avec les données actuelles
        $this->type = $this->programme->type;
        $this->code = $this->programme->code;
        $this->name = $this->programme->name;
        $this->credits = $this->programme->credits;
        $this->coefficient = $this->programme->coefficient;
        $this->semestre_id = $this->programme->semestre_id;
        $this->niveau_id = $this->programme->niveau_id;
        $this->parcour_id = $this->programme->parcour_id;
        $this->parent_id = $this->programme->parent_id;
        $this->status = $this->programme->status;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'programmeId',
            'programme',
            'type',
            'code',
            'name',
            'credits',
            'coefficient',
            'semestre_id',
            'niveau_id',
            'parcour_id',
            'parent_id',
        ]);
        $this->status = true;
    }

    public function updateProgramme()
    {
        if (!$this->programmeId) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'ID du programme manquant',
            ]);
            return;
        }

        // VALIDATION
        $validated = $this->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('programmes', 'code')
                    ->ignore($this->programmeId, 'id')
                    ->where(function ($query) {
                        if ($this->parent_id) {
                            $query->where('parent_id', $this->parent_id);
                        } else {
                            $query->whereNull('parent_id');
                        }
                    })
            ],
            'name' => 'required|string|max:255',
            'credits' => 'nullable|integer|min:1|max:60',
            'coefficient' => 'nullable|numeric|min:0.5|max:10',
            'semestre_id' => 'required|exists:semestres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
            'status' => 'boolean',
        ], [
            'code.required' => 'Le code est requis',
            'code.unique' => $this->parent_id 
                ? 'Ce code existe déjà pour cette UE' 
                : 'Ce code existe déjà',
            'name.required' => 'Le nom est requis',
            'semestre_id.required' => 'Le semestre est requis',
            'niveau_id.required' => 'Le niveau est requis',
            'parcour_id.required' => 'Le parcours est requis',
            'credits.min' => 'Les crédits doivent être au moins 1',
            'credits.max' => 'Les crédits ne peuvent pas dépasser 60',
        ]);

        try {
            $programme = Programme::findOrFail($this->programmeId);
            
            $programme->update([
                'code' => $this->code,
                'name' => $this->name,
                'credits' => $this->credits,
                'coefficient' => $this->coefficient,
                'semestre_id' => $this->semestre_id,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'status' => $this->status,
            ]);

            $this->alert('success', 'Succès !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Programme modifié avec succès',
            ]);

            $this->closeModal();
            $this->dispatch('programmeUpdated');
            
        } catch (\Exception $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 4000,
                'toast' => true,
                'text' => 'Erreur lors de la modification : ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.edit-programme', [
            'semestres' => Semestre::orderBy('id')->get(),
            'niveaux' => Niveau::orderBy('id')->get(),
            'parcours' => Parcour::orderBy('id')->get(),
        ]);
    }
}