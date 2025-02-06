<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Niveau;

class Niveaux extends Component
{
    use WithPagination;

    public $search = '';
    public $levelId = null;
    public $name = '';
    public $sigle = '';
    public $status = true;
    public $showLevelModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sigle' => 'required|string|max:10',
    ];

    public function render()
    {
        $niveaux = Niveau::query()
            ->when($this->search, fn($query) =>
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sigle', 'like', "%{$this->search}%")
            )
            ->paginate(10);

        return view('livewire.admin.niveaux', [
            'niveaux' => $niveaux,
        ]);
    }

    public function toggleLevelStatus($id)
    {
        $niveau = Niveau::findOrFail($id);
        $niveau->update(['status' => !$niveau->status]);
    }

    public function editLevel($id)
    {
        $niveau = Niveau::findOrFail($id);

        $this->levelId = $niveau->id;
        $this->name = $niveau->name;
        $this->sigle = $niveau->sigle;
        $this->status = $niveau->status;
        $this->showLevelModal = true;
    }

    public function deleteLevel($id)
    {
        Niveau::findOrFail($id)->delete();
    }

    public function saveLevel()
    {
        $this->validate();

        $action = $this->levelId ? 'mis à jour' : 'créé';

        Niveau::updateOrCreate(
            ['id' => $this->levelId],
            [
                'name' => $this->name,
                'sigle' => $this->sigle,
                'status' => $this->status,
            ]
        );

        $message = "Niveau $action avec succès";

        $this->resetForm();
        session()->flash('status', $message);
        $this->showLevelModal = false;
    }

    public function resetForm()
    {
        $this->levelId = null;
        $this->name = '';
        $this->sigle = '';
        $this->status = true;
        $this->showLevelModal = false;
    }
}
