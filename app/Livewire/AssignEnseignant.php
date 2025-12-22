<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Programme;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssignEnseignant extends Component
{
    use LivewireAlert;

    public $showModal = false;
    public $programmeId;
    public $programme;
    public $selectedEnseignant;
    public $heures_cm = 0;
    public $heures_td = 0;
    public $heures_tp = 0;
    public $note = '';
    public $enseignantActuel = null; // Pour afficher l'enseignant actuel

    protected $rules = [
        'selectedEnseignant' => 'required|exists:users,id',
        'heures_cm' => 'required|integer|min:0|max:200',
        'heures_td' => 'required|integer|min:0|max:200',
        'heures_tp' => 'required|integer|min:0|max:200',
        'note' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'selectedEnseignant.required' => 'Veuillez sélectionner un enseignant',
        'heures_cm.required' => 'Les heures CM sont requises',
        'heures_cm.min' => 'Les heures CM doivent être positives',
        'heures_cm.max' => 'Maximum 200 heures CM',
        'heures_td.required' => 'Les heures TD sont requises',
        'heures_td.min' => 'Les heures TD doivent être positives',
        'heures_td.max' => 'Maximum 200 heures TD',
        'heures_tp.required' => 'Les heures TP sont requises',
        'heures_tp.min' => 'Les heures TP doivent être positives',
        'heures_tp.max' => 'Maximum 200 heures TP',
    ];

    #[On('openAssignModal')]
    public function openModal($programmeId)
    {
        $this->resetForm();
        $this->resetValidation();
        
        $this->programmeId = $programmeId;
        $this->programme = Programme::with('parent', 'enseignants.profil')->find($programmeId);
        
        if (!$this->programme || !$this->programme->isEc()) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Seuls les ECs peuvent avoir un enseignant assigné',
            ]);
            return;
        }

        // Récupérer l'enseignant actuel s'il existe
        $enseignantActuel = $this->programme->enseignants->first();
        
        if ($enseignantActuel) {
            $this->enseignantActuel = $enseignantActuel;
            $this->selectedEnseignant = $enseignantActuel->id;
            $this->heures_cm = $enseignantActuel->pivot->heures_cm;
            $this->heures_td = $enseignantActuel->pivot->heures_td;
            $this->heures_tp = $enseignantActuel->pivot->heures_tp;
            $this->note = $enseignantActuel->pivot->note;
        }

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
            'selectedEnseignant',
            'heures_cm',
            'heures_td',
            'heures_tp',
            'note',
            'programmeId',
            'programme',
            'enseignantActuel'
        ]);
        
        $this->heures_cm = 0;
        $this->heures_td = 0;
        $this->heures_tp = 0;
    }

    public function assignEnseignant()
    {
        $this->validate();

        try {
            $enseignant = User::findOrFail($this->selectedEnseignant);

            // Vérifier que c'est bien un enseignant
            if (!$enseignant->hasRole('teacher')) {
                $this->alert('error', 'Erreur', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                    'text' => 'L\'utilisateur sélectionné n\'est pas un enseignant',
                ]);
                return;
            }

            // SYNC : Remplace l'ancien enseignant par le nouveau (UN SEUL enseignant)
            $this->programme->enseignants()->sync([
                $enseignant->id => [
                    'heures_cm' => $this->heures_cm,
                    'heures_td' => $this->heures_td,
                    'heures_tp' => $this->heures_tp,
                    'is_responsable' => true, // Toujours responsable car c'est le seul
                    'note' => $this->note,
                ]
            ]);

            $action = $this->enseignantActuel ? 'modifié' : 'assigné';

            $this->alert('success', 'Succès !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => "Enseignant {$action} avec succès",
            ]);
            
            $this->closeModal();
            $this->dispatch('enseignantAssigned');
            
        } catch (\Exception $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 4000,
                'toast' => true,
                'text' => 'Erreur lors de l\'assignation : ' . $e->getMessage(),
            ]);
        }
    }

    public function retirerEnseignant()
    {
        try {
            $this->programme->enseignants()->detach();

            $this->alert('success', 'Retiré !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Enseignant retiré avec succès',
            ]);

            $this->closeModal();
            $this->dispatch('enseignantAssigned');
            
        } catch (\Exception $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'top-end',
                'timer' => 4000,
                'toast' => true,
                'text' => 'Erreur lors de la suppression : ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $enseignants = User::role('teacher')
            ->where('status', true)
            ->with('profil')
            ->orderBy('name')
            ->get();

        return view('livewire.assign-enseignant', [
            'enseignants' => $enseignants,
        ]);
    }
}