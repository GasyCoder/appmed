<?php

namespace App\Livewire\Programmes;

use App\Models\Programme;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSemestre() { $this->resetPage(); }

    public function updatingAnnee()
    {
        $this->resetPage();
        $this->semestre = null;
    }

    public function toggleShowEnseignants()
    {
        $this->showEnseignants = !$this->showEnseignants;
    }

    #[On('enseignantAssigned')]
    #[On('programmeUpdated')]
    #[On('programmeDeleted')]
    public function refreshProgrammes()
    {
        $this->resetPage();
    }

    /**
     * ✅ Détermine la liste des semestres visibles selon le rôle & niveau étudiant.
     */
    public function getSemestresProperty()
    {
        $user = Auth::user();

        // Admin/Teacher : logique actuelle (année 4 => S1-2, année 5 => S3-4, sinon tout)
        if (!$user || !$user->hasRole('student')) {
            if ((int) $this->annee === 4) {
                return Semestre::whereIn('id', [1, 2])->orderBy('id')->get();
            }
            if ((int) $this->annee === 5) {
                return Semestre::whereIn('id', [3, 4])->orderBy('id')->get();
            }
            return Semestre::orderBy('id')->get();
        }

        // Student : restreindre selon M1/M2
        $user->loadMissing('niveau:id,name,sigle');

        $sigle = strtoupper((string) ($user->niveau->sigle ?? $user->niveau->name ?? ''));

        if ($sigle === 'M2') {
            return Semestre::whereIn('id', [3, 4])->orderBy('id')->get();
        }

        // défaut M1
        return Semestre::whereIn('id', [1, 2])->orderBy('id')->get();
    }

    public function render()
    {
        $user = Auth::user();
        if ($user && $user->hasRole('student')) {
            $user->loadMissing('niveau:id,name,sigle');
        }

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
            ->active();

        // ✅ Filtrage étudiant (sécurité)
        if ($user && $user->hasRole('student')) {
            $query->visibleForStudent($user);

            // On force aussi le mapping semestre, même si niveau_id est bon
            $sigle = strtoupper((string) ($user->niveau->sigle ?? $user->niveau->name ?? ''));
            if ($sigle === 'M2') {
                $query->whereIn('semestre_id', Programme::semestresForM2());
            } else {
                $query->whereIn('semestre_id', Programme::semestresForM1());
            }

            // Optionnel: si tu ne veux pas que l’étudiant change annee manuellement:
            // on écrase automatiquement $annee pour cohérence UI
            $this->annee = ($sigle === 'M2') ? 5 : 4;

            // Si semestre sélectionné hors plage, on le reset
            if ($this->semestre && !in_array((int)$this->semestre, ($sigle === 'M2') ? [3,4] : [1,2], true)) {
                $this->semestre = null;
            }
        }

        // ✅ Search
        $query->when($this->search, function ($query) {
            $s = $this->search;

            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%')
                  ->orWhere('code', 'like', '%' . $s . '%')
                  ->orWhereHas('elements', function ($q) use ($s) {
                      $q->where('name', 'like', '%' . $s . '%')
                        ->orWhere('code', 'like', '%' . $s . '%')
                        ->orWhereHas('enseignants', function ($eq) use ($s) {
                            $eq->where('name', 'like', '%' . $s . '%')
                               ->orWhere('email', 'like', '%' . $s . '%')
                               ->orWhereHas('profil', function ($pq) use ($s) {
                                   $pq->where('grade', 'like', '%' . $s . '%');
                               });
                        });
                  });
            });
        });

        // ✅ Filtres année/semestre (admin/teacher), pour étudiant on les laisse mais sécurisés par whereIn déjà
        $query->when($this->annee, fn($q) => $q->byAnnee($this->annee));
        $query->when($this->semestre, fn($q) => $q->bySemestre($this->semestre));

        $query->orderBy('semestre_id')->orderBy('order');

        // ✅ Stats (même périmètre)
        $stats = $this->calculateStats($user);

        return view('livewire.programmes.programme-index', [
            'programmes' => $query->paginate($this->perPage),
            'stats' => $stats
        ]);
    }

    private function calculateStats(?User $user): array
    {
        $baseQuery = Programme::query()->active();

        // ✅ Appliquer le même filtre étudiant
        if ($user && $user->hasRole('student')) {
            $user->loadMissing('niveau:id,name,sigle');

            $baseQuery->visibleForStudent($user);

            $sigle = strtoupper((string) ($user->niveau->sigle ?? $user->niveau->name ?? ''));
            if ($sigle === 'M2') {
                $baseQuery->whereIn('semestre_id', Programme::semestresForM2());
            } else {
                $baseQuery->whereIn('semestre_id', Programme::semestresForM1());
            }
        }

        // Pour enseignants total, tu peux décider:
        // - global (comme avant)
        // - ou seulement enseignants présents dans le périmètre étudiant (plus complexe)
        // Ici je garde global pour admin/teacher, et pour student on laisse global (ou tu peux mettre 0).
        $totalTeachers = User::activeTeachers()->count();

        return [
            'totalUE' => $baseQuery->clone()->ues()->count(),
            'totalEC' => $baseQuery->clone()->ecs()->count(),
            'totalEnseignants' => $totalTeachers,
            'ecSansEnseignant' => $baseQuery->clone()->ecs()->withoutEnseignants()->count(),

            'annee4' => [
                'ue' => $baseQuery->clone()->ues()->byAnnee(4)->count(),
                'ec' => $baseQuery->clone()->ecs()->byAnnee(4)->count(),
            ],
            'annee5' => [
                'ue' => $baseQuery->clone()->ues()->byAnnee(5)->count(),
                'ec' => $baseQuery->clone()->ecs()->byAnnee(5)->count(),
            ],

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
