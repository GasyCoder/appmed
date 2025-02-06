<?php

namespace App\Livewire\Admin;

use App\Models\Parcour;
use Livewire\Component;
use Livewire\WithPagination;

class Parcours extends Component
{
    use WithPagination, WithPagination;

    public $search = '';
    public $parcourId = null;
    public $name = '';
    public $sigle = '';
    public $status = true;
    public $showParcourModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sigle' => 'required|string|max:10',
    ];
    public function render()
    {
        $parcours = Parcour::query()
        ->when($this->search, fn($query) =>
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('sigle', 'like', "%{$this->search}%")
        )
        ->paginate(10);
        return view('livewire.admin.parcours', [
            'parcours' => $parcours,
        ]);
    }

    public function toggleParcourStatus($id)
    {
        $parcour = Parcour::findOrFail($id);
        $parcour->update(['status' => !$parcour->status]);
    }

    public function editParcour($id)
    {
        $parcour = Parcour::findOrFail($id);

        $this->parcourId = $parcour->id;
        $this->name = $parcour->name;
        $this->sigle = $parcour->sigle;
        $this->status = $parcour->status;
        $this->showParcourModal = true;
    }

    public function deleteParcour($id)
    {
        Parcour::findOrFail($id)->delete();
    }

    public function saveParcour()
    {
        $this->validate();

        $action = $this->parcourId ? 'mis à jour' : 'créé';

        Parcour::updateOrCreate(
            ['id' => $this->parcourId],
            [
                'name' => $this->name,
                'sigle' => $this->sigle,
                'status' => $this->status,
            ]
        );

        $message = "Parcour $action avec succès";

        $this->resetForm();
        session()->flash('status', $message);
        $this->showParcourModal = false;
    }

    public function resetForm()
    {
        $this->parcourId = null;
        $this->name = '';
        $this->sigle = '';
        $this->status = true;
        $this->showParcourModal = false;
    }
}
