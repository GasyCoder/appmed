<?php

namespace App\Livewire\Student;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardStudent extends Component
{
    public function mount(): void
    {
        // Sécurité : seul étudiant
        if (!Auth::check() || !Auth::user()->hasRole('student')) {
            redirect()->route('login')->send();
        }
    }

    public function getCurrentDateTimeProperty(): Carbon
    {
        return now();
    }

    public function getStudentProperty()
    {
        return Auth::user()->load(['niveau', 'parcour', 'profil']);
    }

    /**
     * Base query des "cours" (documents accessibles pour l'étudiant)
     * IMPORTANT : accepte aussi les documents "communs" avec niveau_id/parcour_id NULL
     */
    private function baseDocumentsQuery()
    {
        $u = Auth::user();

        return Document::query()
            ->where('is_actif', true)
            ->when($u->niveau_id, function ($q) use ($u) {
                $q->where(function ($qq) use ($u) {
                    $qq->whereNull('niveau_id')
                       ->orWhere('niveau_id', $u->niveau_id);
                });
            })
            ->when($u->parcour_id, function ($q) use ($u) {
                $q->where(function ($qq) use ($u) {
                    $qq->whereNull('parcour_id')
                       ->orWhere('parcour_id', $u->parcour_id);
                });
            });
    }

    public function getStatsProperty(): array
    {
        $q = $this->baseDocumentsQuery();

        // on clone pour éviter les effets de bord
        $total = (clone $q)->count();

        $todayCount = (clone $q)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $weekCount = (clone $q)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $viewsSum = (clone $q)->sum('view_count');
        $downloadsSum = (clone $q)->sum('download_count');

        return [
            'total' => $total,
            'today' => $todayCount,
            'week' => $weekCount,
            'views' => (int) $viewsSum,
            'downloads' => (int) $downloadsSum,
        ];
    }

    public function getTodayDocumentsProperty()
    {
        return $this->baseDocumentsQuery()
            ->with(['uploader.profil'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->take(6)
            ->get();
    }

    public function getRecentDocumentsProperty()
    {
        return $this->baseDocumentsQuery()
            ->with(['uploader.profil'])
            ->latest()
            ->take(8)
            ->get();
    }

    public function getPopularDocumentsProperty()
    {
        return $this->baseDocumentsQuery()
            ->with(['uploader.profil'])
            ->orderByDesc('view_count')
            ->latest()
            ->take(6)
            ->get();
    }

    public function getTeachersProperty()
    {
        $u = Auth::user();

        return User::query()
            ->role('teacher')
            ->whereHas('documents', function ($q) use ($u) {
                $q->where('is_actif', true)
                  ->when($u->niveau_id, function ($qq) use ($u) {
                      $qq->where(function ($x) use ($u) {
                          $x->whereNull('niveau_id')
                            ->orWhere('niveau_id', $u->niveau_id);
                      });
                  })
                  ->when($u->parcour_id, function ($qq) use ($u) {
                      $qq->where(function ($x) use ($u) {
                          $x->whereNull('parcour_id')
                            ->orWhere('parcour_id', $u->parcour_id);
                      });
                  });
            })
            ->with(['profil'])
            ->withCount(['documents as docs_count' => function ($q) use ($u) {
                $q->where('is_actif', true)
                  ->when($u->niveau_id, function ($qq) use ($u) {
                      $qq->where(function ($x) use ($u) {
                          $x->whereNull('niveau_id')
                            ->orWhere('niveau_id', $u->niveau_id);
                      });
                  })
                  ->when($u->parcour_id, function ($qq) use ($u) {
                      $qq->where(function ($x) use ($u) {
                          $x->whereNull('parcour_id')
                            ->orWhere('parcour_id', $u->parcour_id);
                      });
                  });
            }])
            ->orderByDesc('docs_count')
            ->take(6)
            ->get();
    }

    public function getUserSessionsProperty()
    {
        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->take(5)
            ->get()
            ->map(function ($session) {
                return (object)[
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                ];
            });
    }

    public function getLastLoginAtProperty(): ?Carbon
    {
        $last = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->value('last_activity');

        return $last ? Carbon::createFromTimestamp($last) : null;
    }

    public function shortAgent(?string $agent): string
    {
        return Str::limit($agent ?? '-', 90);
    }

    public function render()
    {
        return view('livewire.student.dashboard-student', [
            'student' => $this->student,
            'stats' => $this->stats,
            'todayDocuments' => $this->todayDocuments,
            'recentDocuments' => $this->recentDocuments,
            'popularDocuments' => $this->popularDocuments,
            'teachers' => $this->teachers,
            'currentDateTime' => $this->currentDateTime,
            'userSessions' => $this->userSessions,
            'lastLoginAt' => $this->lastLoginAt,
        ]);
    }
}
