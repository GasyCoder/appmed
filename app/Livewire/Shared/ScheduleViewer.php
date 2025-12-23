<?php

namespace App\Livewire\Shared;

use App\Models\Schedule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ScheduleViewer extends Component
{
    public $typeFilter = '';

    public function viewSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule) {
            $schedule->increment('view_count');
        }
    }

    public function downloadSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule && Storage::disk('public')->exists($schedule->file_path)) {
            $schedule->increment('download_count');
            
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
            ->with(['niveau', 'parcour'])
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
            ->when($user->hasRole('student'), function($query) use ($user) {
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

        return view('livewire.shared.schedule-viewer', [
            'schedules' => $schedules,
        ]);
    }
}