<?php

namespace App\Livewire\Shared;

use App\Models\Schedule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ScheduleViewer extends Component
{
    public $typeFilter = '';
    public $viewedFilter = 'all'; // ✅ NOUVEAU : 'all', 'viewed', 'unviewed'
    public bool $isStudent = false;

    public function mount()
    {
        $this->isStudent = Auth::check() && Auth::user()->hasRole('student');
    }

    // ✅ NOUVEAU : Changer le filtre vu/non vu
    public function setViewedFilter($filter)
    {
        $this->viewedFilter = $filter;
    }

    public function downloadSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule && Storage::disk('public')->exists($schedule->file_path)) {
            $schedule->registerDownload(Auth::user());
            
            return Storage::disk('public')->download(
                $schedule->file_path,
                $schedule->title . '.' . pathinfo($schedule->file_path, PATHINFO_EXTENSION)
            );
        }
        
        session()->flash('error', 'Fichier introuvable.');
    }

    public function render()
    {
        $user = Auth::user();
        
        $schedules = Schedule::query()
            ->with(['niveau', 'parcour', 'uploader'])
            ->where('is_active', true)
            ->where(function($query) {
                $now = now();
                $query->where(function($q) use ($now) {
                    $q->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
                })->where(function($q) use ($now) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
                });
            })
            ->when($this->typeFilter, function($query) {
                $query->where('type', $this->typeFilter);
            })
            // ✅ NOUVEAU : Filtre vu/non vu
            ->when($this->viewedFilter === 'viewed' && $user, function($query) use ($user) {
                $query->whereHas('views', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->when($this->viewedFilter === 'unviewed' && $user, function($query) use ($user) {
                $query->whereDoesntHave('views', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->when($user && $user->hasRole('student'), function($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->whereNull('niveau_id')
                      ->orWhere('niveau_id', $user->niveau_id);
                })
                ->where(function($q) use ($user) {
                    $q->whereNull('parcour_id')
                      ->orWhere('parcour_id', $user->parcour_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ NOUVEAU : Compteurs pour les badges
        $viewedCount = 0;
        $unviewedCount = 0;

        if ($user && $user->hasRole('student')) {
            $baseQuery = Schedule::where('is_active', true)
                ->where(function($q) use ($user) {
                    $q->whereNull('niveau_id')->orWhere('niveau_id', $user->niveau_id);
                })
                ->where(function($q) use ($user) {
                    $q->whereNull('parcour_id')->orWhere('parcour_id', $user->parcour_id);
                });

            $viewedCount = (clone $baseQuery)
                ->whereHas('views', fn($q) => $q->where('user_id', $user->id))
                ->count();

            $unviewedCount = (clone $baseQuery)
                ->whereDoesntHave('views', fn($q) => $q->where('user_id', $user->id))
                ->count();
        }

        return view('livewire.shared.schedule-viewer', [
            'schedules' => $schedules,
            'viewedCount' => $viewedCount,
            'unviewedCount' => $unviewedCount,
        ]);
    }
}