<?php

namespace App\Livewire\Student;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Document;
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

    public function mount()
    {
        $this->student = Auth::user();
        $this->currentDateTime = Carbon::now();
        $this->lastLoginAt = $this->student?->last_login_at ?? null;

        $base = $this->documentsBaseQuery();

        // STATS
        $this->stats['total'] = (clone $base)->count();
        $this->stats['today'] = (clone $base)->whereDate('created_at', Carbon::today())->count();
        $this->stats['views'] = (int) (clone $base)->sum('view_count');
        $this->stats['downloads'] = (int) (clone $base)->sum('download_count');

        // DOCUMENTS RECENTS
        $this->recentDocuments = (clone $base)
            ->latest()
            ->take(6)
            ->get();

        // ENSEIGNANTS (FK = uploaded_by)
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
            ->where('niveau_id', $u->niveau_id);

        if (!empty($u->parcour_id) && Schema::hasColumn('documents', 'parcour_id')) {
            $q->where('parcour_id', $u->parcour_id);
        }

        return $q;
    }

    public function render()
    {
        return view('livewire.student.home-student');
    }
}
