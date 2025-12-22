<?php

namespace App\Livewire\Programmes;

use App\Models\Programme;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DeleteProgramme extends Component
{
    use LivewireAlert;

    public $showModal = false;
    public $programmeId;
    public $programme;

    #[On('openDeleteModal')]
    public function openModal($programmeId)
    {
        $this->programmeId = $programmeId;
        $this->programme = Programme::with('parent', 'enseignants', 'elements')->findOrFail($programmeId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['programmeId', 'programme']);
    }

    public function deleteProgramme()
    {
        try {
            // Vérifier si c'est une UE avec des ECs
            if ($this->programme->isUe() && $this->programme->elements->count() > 0) {
                $this->alert('error', 'Impossible !', [
                    'position' => 'top-end',
                    'timer' => 4000,
                    'toast' => true,
                    'text' => 'Impossible de supprimer cette UE car elle contient des ECs',
                ]);
                $this->closeModal();
                return;
            }

            // Détacher les enseignants si c'est un EC
            if ($this->programme->isEc()) {
                $this->programme->enseignants()->detach();
            }

            // Supprimer le programme
            $this->programme->delete();

            $this->alert('success', 'Supprimé !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Programme supprimé avec succès',
            ]);

            $this->closeModal();
            $this->dispatch('programmeDeleted');
            
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
        return view('livewire.programmes.delete-programme');
    }
}