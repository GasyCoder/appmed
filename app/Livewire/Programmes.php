<?php

namespace App\Livewire;

use App\Models\Programme;
use Livewire\Component;
use Livewire\WithPagination;

class Programmes extends Component
{
    use WithPagination;

    public $search = '';
    public $semestre = null;

    protected $queryString = ['search', 'semestre'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Requête pour les UEs
        $query = Programme::query()
            ->where('type', 'UE')
            ->with(['elements' => function($q) {
                $q->orderBy('order');
            }])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('code', 'like', '%'.$this->search.'%')
                      ->orWhereHas('elements', function($q) {
                          $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('code', 'like', '%'.$this->search.'%');
                      });
                });
            })
            ->when($this->semestre, function($query) {
                $query->where('semestre_id', $this->semestre);
            })
            ->orderBy('semestre_id')
            ->orderBy('order');

        // Statistiques
        $stats = [
            'totalUE' => Programme::where('type', 'UE')->count(),
            'totalEC' => Programme::where('type', 'EC')->count(),
            'semestre1' => [
                'ue' => Programme::where('type', 'UE')->where('semestre_id', 1)->count(),
                'ec' => Programme::where('type', 'EC')->where('semestre_id', 1)->count(),
            ],
            'semestre2' => [
                'ue' => Programme::where('type', 'UE')->where('semestre_id', 2)->count(),
                'ec' => Programme::where('type', 'EC')->where('semestre_id', 2)->count(),
            ],
        ];

        return view('livewire.programmes', [
            'programmes' => $query->paginate(10),
            'stats' => $stats
        ]);
    }
}
