<?php

namespace App\Livewire\Admin;

use App\Models\Semestre;
use Livewire\Component;
use Livewire\WithPagination;

class Semestres extends Component
{
    use WithPagination;

    public $search = '';
    public $semestreId = null;
    public $name = '';
    public $niveau_id = ''; // Ajout du niveau_id
    public $is_active = false; // Changement de status à is_active
    public $status = true;
    public $showSemestreModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'niveau_id' => 'required|exists:niveaux,id', // Ajout de la validation pour niveau_id
        'is_active' => 'boolean',
        'status' => 'boolean'
    ];

    public function render()
    {
        $semestres = Semestre::with('niveau')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.semestres', [
            'semestres' => $semestres
        ]);
    }

    public function toggleSemestreActive($id)
    {
        $semestre = Semestre::findOrFail($id);
        $semestre->update(['is_active' => !$semestre->is_active]);
    }

    public function toggleSemestreStatus($id)
    {
        $semestre = Semestre::findOrFail($id);
        $semestre->update(['status' => !$semestre->status]);
    }

    public function editSemestre($id)
    {
        $semestre = Semestre::findOrFail($id);

        $this->semestreId = $semestre->id;
        $this->name = $semestre->name;
        $this->niveau_id = $semestre->niveau_id;
        $this->is_active = $semestre->is_active;
        $this->status = $semestre->status;
        $this->showSemestreModal = true;
    }

    public function deleteSemestre($id)
    {
        Semestre::findOrFail($id)->delete();
        session()->flash('status', 'Semestre supprimé avec succès');
    }

    public function saveSemestre()
    {
        $this->validate();

        $action = $this->semestreId ? 'mis à jour' : 'créé';

        Semestre::updateOrCreate(
            ['id' => $this->semestreId],
            [
                'name' => $this->name,
                'niveau_id' => $this->niveau_id,
                'is_active' => $this->is_active,
                'status' => $this->status,
            ]
        );

        $message = "Semestre $action avec succès";

        $this->resetForm();
        session()->flash('status', $message);
        $this->showSemestreModal = false;
    }

    public function resetForm()
    {
        $this->semestreId = null;
        $this->name = '';
        $this->niveau_id = '';
        $this->is_active = false;
        $this->status = true;
        $this->showSemestreModal = false;
    }
}
