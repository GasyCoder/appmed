<?php

namespace App\Livewire\Student;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Document;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeStudent extends Component
{
    public $student;

    public array $stats = [
        'total' => 0,
        'today' => 0,
        'views' => 0,
        'downloads' => 0,
    ];

    public $recentDocuments;
    public $teachers;

    public $currentDateTime;
    public $lastLoginAt;

    public array $menuStats = [
        'documents' => 0,
        'schedules' => 0,
    ];

    public int $annCount = 0;

    public array $primaryMenus = [];
    public array $supportMenus = [];

    // ✅ NOUVEAU : Méthodes pour les icônes
    public function getIconTone(string $icon): string
    {
        return match($icon) {
            'doc' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
            'calendar' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
            'users' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
            'book' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
            default => 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400',
        };
    }

    public function getIconSvg(string $icon): string
    {
        $svgs = [
            'doc' => '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            
            'calendar' => '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            
            'users' => '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            
            'book' => '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
        ];

        return $svgs[$icon] ?? '<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
    }

    public function mount(): void
    {
        $this->student = Auth::user();
        $this->currentDateTime = Carbon::now();
        $this->lastLoginAt = $this->student?->last_login_at ?? null;

        if (!$this->student) {
            $this->recentDocuments = collect();
            $this->teachers = collect();
            return;
        }

        $base = $this->documentsBaseQuery();

        $this->stats['total'] = (clone $base)->count();
        $this->stats['today'] = (clone $base)->whereDate('created_at', Carbon::today())->count();
        $this->stats['views'] = (int) (clone $base)->sum('view_count');
        $this->stats['downloads'] = (int) (clone $base)->sum('download_count');

        $this->recentDocuments = (clone $base)
            ->latest()
            ->take(6)
            ->get();

        $teacherAgg = (clone $base)
            ->select('uploaded_by', DB::raw('COUNT(*) as docs_count'))
            ->whereNotNull('uploaded_by')
            ->groupBy('uploaded_by')
            ->orderByDesc('docs_count')
            ->take(8)
            ->get();

        $ids = $teacherAgg->pluck('uploaded_by')->filter()->unique()->values()->all();

        $teachers = User::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $this->teachers = $teacherAgg->map(function ($row) use ($teachers) {
            $t = $teachers->get($row->uploaded_by);
            if (!$t) return null;

            $t->docs_count = (int) $row->docs_count;
            return $t;
        })->filter()->values();
    }

    private function documentsBaseQuery()
    {
        $u = Auth::user();

        $q = Document::query()
            ->with('uploader')
            ->where('is_actif', true)
            ->where('niveau_id', $u->niveau_id)
            ->where(function ($qq) {
                $qq->whereNull('is_archive')->orWhere('is_archive', 0);
            });

        if (!empty($u->parcour_id) && Schema::hasColumn('documents', 'parcour_id')) {
            $q->where('parcour_id', $u->parcour_id);
        }

        return $q;
    }

    private function schedulesBaseQuery()
    {
        $u = Auth::user();

        $q = Schedule::query()
            ->active()
            ->where('niveau_id', $u->niveau_id);

        if (!empty($u->parcour_id) && Schema::hasColumn('schedules', 'parcour_id')) {
            $q->where('parcour_id', $u->parcour_id);
        }

        return $q;
    }

    private function unreadAnnouncementsCount($user): int
    {
        if (!$user) return 0;

        if (!Schema::hasTable('announcements') || !Schema::hasTable('announcement_views')) {
            return 0;
        }

        if (!Schema::hasColumn('announcements', 'is_active')) return 0;

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()->all()
            : [];

        $q = DB::table('announcements')
            ->where('is_active', 1)
            ->where(function ($qq) {
                $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($qq) {
                $qq->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });

        if (Schema::hasColumn('announcements', 'audience_roles')) {
            $q->where(function ($qq) use ($roles) {
                $qq->whereNull('audience_roles');

                foreach ($roles as $r) {
                    $qq->orWhereRaw("JSON_CONTAINS(audience_roles, JSON_QUOTE(?))", [$r]);
                }
            });
        }

        $q->whereNotExists(function ($sub) use ($user) {
            $sub->select(DB::raw(1))
                ->from('announcement_views')
                ->whereColumn('announcement_views.announcement_id', 'announcements.id')
                ->where('announcement_views.user_id', $user->id);
        });

        return (int) $q->count();
    }

    private function buildMenus(): void
    {
        $this->primaryMenus = [
            [
                'label' => 'Mes cours',
                'desc'  => 'Documents, PDF, supports',
                'href'  => route('documents.index'),
                'icon'  => 'doc',
                'active'=> request()->routeIs('documents.index'),
                'badge' => $this->menuStats['documents'] ?? 0,
                'badgeColor' => 'bg-red-500',
            ],
            [
                'label' => 'Emploi du temps',
                'desc'  => 'Planning et horaires',
                'href'  => route('student.timetable'),
                'icon'  => 'calendar',
                'active'=> request()->routeIs('student.timetable'),
                'badge' => $this->menuStats['schedules'] ?? 0,
                'badgeColor' => 'bg-emerald-500',
            ],
            [
                'label' => 'Mes enseignants',
                'desc'  => 'Liste des enseignants',
                'href'  => route('student.myTeacher'),
                'icon'  => 'users',
                'active'=> request()->routeIs('student.myTeacher'),
                'badge' => null,
                'badgeColor' => 'bg-blue-500',
            ],
            [
                'label' => 'Programmes',
                'desc'  => 'Consulter les programmes',
                'href'  => route('programs'),
                'icon'  => 'book',
                'active'=> request()->routeIs('programs'),
                'badge' => null,
                'badgeColor' => 'bg-gray-500',
            ],
        ];

        $this->supportMenus = [
            [
                'label' => 'Archives des cours',
                'desc'  => 'Anciens documents',
                'href'  => route('documents.index', ['scope' => 'archives']),
                'icon'  => 'doc',
                'active'=> false,
                'badge' => null,
                'badgeColor' => 'bg-gray-500',
            ],
            [
                'label' => 'Archives emploi du temps',
                'desc'  => 'Plannings précédents',
                'href'  => route('student.timetable', ['scope' => 'archives']),
                'icon'  => 'calendar',
                'active'=> false,
                'badge' => null,
                'badgeColor' => 'bg-gray-500',
            ],
        ];
    }

    public function render()
    {
        $u = Auth::user();

        $unviewedDocumentsCount = $this->documentsBaseQuery()
            ->whereDoesntHave('views', function ($q) use ($u) {
                $q->where('user_id', $u->id);
            })
            ->count();

        $unviewedSchedulesCount = $this->schedulesBaseQuery()
            ->unviewedBy($u)
            ->count();

        $this->menuStats = [
            'documents' => (int) $unviewedDocumentsCount,
            'schedules' => (int) $unviewedSchedulesCount,
        ];

        $this->annCount = $this->unreadAnnouncementsCount($u);

        $this->buildMenus();

        return view('livewire.student.home-student', [
            'menuStats'    => $this->menuStats,
            'primaryMenus' => $this->primaryMenus,
            'supportMenus' => $this->supportMenus,
            'annCount'     => $this->annCount,
        ]);
    }
}