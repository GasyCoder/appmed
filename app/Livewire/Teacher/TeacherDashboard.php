<?php

namespace App\Livewire\Teacher;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends Component
{
    public function mount()
    {
        abort_if(!Auth::check() || !Auth::user()->hasRole('teacher'), 403);
    }

    public function render()
    {
        $userId = Auth::id();

        $user = Auth::user()->load([
            'profil',
            'teacherParcours',
            'teacherNiveaux.semestres' => function ($q) {
                // chez toi tu filtres déjà sur status=true, on garde
                $q->where('status', true)->orderBy('name');
            },
        ]);

        $baseDocs = Document::query()->where('uploaded_by', $userId);

        $loginActivities = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->take(5)
            ->get();

        $lastLoginAt = $loginActivities->first()
            ? Carbon::createFromTimestamp($loginActivities->first()->last_activity)
            : null;

        $stats = [
            'total_uploads'     => (clone $baseDocs)->count(),
            'public_documents'  => (clone $baseDocs)->where('is_actif', true)->count(),
            'pending_documents' => (clone $baseDocs)->where('is_actif', false)->count(),
            'total_downloads'   => (int) (clone $baseDocs)->sum('download_count'),
            'total_views'       => (int) (clone $baseDocs)->sum('view_count'),
            'niveaux_count'     => $user->teacherNiveaux->count(),
            'parcours_count'    => $user->teacherParcours->count(),
        ];

        $recentDocuments = (clone $baseDocs)
            ->with(['niveau', 'parcour', 'semestre'])
            ->latest()
            ->take(6)
            ->get();

        $monthlyStats = (clone $baseDocs)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // Optim: compter les documents par (niveau_id, semestre_id) en 1 seule requête
        $semestreCounts = (clone $baseDocs)
            ->selectRaw('niveau_id, semestre_id, COUNT(*) as cnt')
            ->groupBy('niveau_id', 'semestre_id')
            ->get()
            ->mapWithKeys(fn ($r) => [
                ($r->niveau_id . '-' . $r->semestre_id) => (int) $r->cnt
            ]);

        $niveauxSemestres = $user->teacherNiveaux->map(function ($niveau) use ($semestreCounts) {
            return [
                'id' => $niveau->id,
                'name' => $niveau->name,
                'semestres' => $niveau->semestres->map(function ($semestre) use ($niveau, $semestreCounts) {
                    $isActive = (bool) ($semestre->is_active ?? $semestre->status ?? false);
                    $key = $niveau->id . '-' . $semestre->id;

                    return [
                        'id' => $semestre->id,
                        'name' => $semestre->name,
                        'is_active' => $isActive,
                        'documents_count' => $semestreCounts[$key] ?? 0,
                    ];
                })->values(),
            ];
        })->values();

        return view('livewire.teacher.teacher-dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentDocuments' => $recentDocuments,
            'monthlyStats' => $monthlyStats,
            'niveauxSemestres' => $niveauxSemestres,
            'loginActivities' => $loginActivities,
            'lastLoginAt' => $lastLoginAt,
        ]);
    }
}
