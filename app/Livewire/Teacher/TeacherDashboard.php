<?php

namespace App\Livewire\Teacher;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TeacherDashboard extends Component
{
    use WithPagination;

    public $selectedTab = 'overview';
    public $search = '';

    public function mount()
    {
        if (!auth()->user()->hasRole('teacher')) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        // Charger l'utilisateur avec les relations nécessaires
        $user = auth()->user()->load([
            'teacherNiveaux.semestres' => function($query) {
                $query->where('status', true)
                      ->orderBy('name');
            },
            'profil'
        ]);

        $stats = [
            'total_uploads' => Document::where('uploaded_by', auth()->id())->count(),
            'public_documents' => Document::where('uploaded_by', auth()->id())
                ->where('is_actif', true)
                ->count(),
            'pending_documents' => Document::where('uploaded_by', auth()->id())
                ->where('is_actif', false)
                ->count(),
            'total_downloads' => Document::where('uploaded_by', auth()->id())
                ->sum('download_count'),
            'niveaux_count' => $user->teacherNiveaux()->count(),
            'parcours_count' => $user->teacherParcours()->count()
        ];

        $recentDocuments = Document::where('uploaded_by', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $monthlyStats = Document::where('uploaded_by', auth()->id())
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // Préparer les niveaux et semestres pour chaque niveau
        $niveauxSemestres = $user->teacherNiveaux->map(function($niveau) {
            return [
                'id' => $niveau->id,
                'name' => $niveau->name,
                'semestres' => $niveau->semestres->map(function($semestre) {
                    return [
                        'id' => $semestre->id,
                        'name' => $semestre->name,
                        'is_active' => $semestre->is_active,
                        'documents_count' => Document::where('uploaded_by', auth()->id())
                            ->where('niveau_id', $semestre->niveau_id)
                            ->where('semestre_id', $semestre->id)
                            ->count()
                    ];
                })
            ];
        });

        return view('livewire.teacher.teacher-dashboard', compact(
            'stats', 
            'recentDocuments', 
            'monthlyStats',
            'user',
            'niveauxSemestres'
        ));
    }
}