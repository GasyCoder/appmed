<?php

namespace App\Livewire\Programmes;

use App\Models\Programme;
use App\Models\Semestre;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ProgrammesIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $semestre = null;
    public $annee = null;
    public $showEnseignants = true;
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'semestre' => ['except' => null],
        'annee' => ['except' => null],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSemestre()
    {
        $this->resetPage();
    }

    public function updatingAnnee()
    {
        $this->resetPage();
        $this->semestre = null;
    }

    public function toggleShowEnseignants()
    {
        $this->showEnseignants = !$this->showEnseignants;
    }

    // Écouter l'événement d'assignation
    #[On('enseignantAssigned')]
    #[On('programmeUpdated')]
    #[On('programmeDeleted')]
    public function refreshProgrammes()
    {
        // Rafraîchir la liste
        $this->render();
    }

    public function getSemestresProperty()
    {
        if ($this->annee == 4) {
            return Semestre::whereIn('id', [1, 2])->orderBy('id')->get();
        } elseif ($this->annee == 5) {
            return Semestre::whereIn('id', [3, 4])->orderBy('id')->get();
        }
        return Semestre::orderBy('id')->get();
    }

    public function render()
    {
        // Requête optimisée pour les UEs avec leurs ECs et enseignants
        $query = Programme::query()
            ->with([
                'elements' => function ($q) {
                    $q->orderBy('order')
                      ->with([
                          'enseignants' => function ($eq) {
                              $eq->select('users.id', 'users.name', 'users.email')
                                 ->with('profil:id,user_id,grade,telephone')
                                 ->orderByPivot('is_responsable', 'desc');
                          }
                      ]);
                },
                'semestre:id,name',
                'niveau:id,name,sigle',
                'parcour:id,name,sigle'
            ])
            ->where('type', Programme::TYPE_UE)
            ->active()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhereHas('elements', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('code', 'like', '%' . $this->search . '%')
                            ->orWhereHas('enseignants', function ($eq) {
                                $eq->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%')
                                   ->orWhereHas('profil', function($pq) {
                                       $pq->where('grade', 'like', '%' . $this->search . '%');
                                   });
                            });
                      });
                });
            })
            ->when($this->annee, function ($query) {
                $query->byAnnee($this->annee);
            })
            ->when($this->semestre, function ($query) {
                $query->bySemestre($this->semestre);
            })
            ->orderBy('semestre_id')
            ->orderBy('order');

        // Statistiques
        $stats = $this->calculateStats();

        return view('livewire.programmes.programme-index', [
            'programmes' => $query->paginate($this->perPage),
            'stats' => $stats
        ]);
    }

    private function calculateStats(): array
    {
        $baseQuery = Programme::query()->active();

        return [
            'totalUE' => $baseQuery->clone()->ues()->count(),
            'totalEC' => $baseQuery->clone()->ecs()->count(),
            'totalEnseignants' => User::activeTeachers()->count(),
            'ecSansEnseignant' => $baseQuery->clone()->ecs()->withoutEnseignants()->count(),
            
            // Stats par année
            'annee4' => [
                'ue' => $baseQuery->clone()->ues()->byAnnee(4)->count(),
                'ec' => $baseQuery->clone()->ecs()->byAnnee(4)->count(),
            ],
            'annee5' => [
                'ue' => $baseQuery->clone()->ues()->byAnnee(5)->count(),
                'ec' => $baseQuery->clone()->ecs()->byAnnee(5)->count(),
            ],
            
            // Stats par semestre
            'semestre1' => [
                'ue' => $baseQuery->clone()->ues()->bySemestre(1)->count(),
                'ec' => $baseQuery->clone()->ecs()->bySemestre(1)->count(),
            ],
            'semestre2' => [
                'ue' => $baseQuery->clone()->ues()->bySemestre(2)->count(),
                'ec' => $baseQuery->clone()->ecs()->bySemestre(2)->count(),
            ],
            'semestre3' => [
                'ue' => $baseQuery->clone()->ues()->bySemestre(3)->count(),
                'ec' => $baseQuery->clone()->ecs()->bySemestre(3)->count(),
            ],
            'semestre4' => [
                'ue' => $baseQuery->clone()->ues()->bySemestre(4)->count(),
                'ec' => $baseQuery->clone()->ecs()->bySemestre(4)->count(),
            ],
        ];
    }
}