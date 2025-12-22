<?php

namespace App\Livewire\Shared;

use App\Models\Schedule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ScheduleViewer extends Component
{
    public $typeFilter = '';
    public $selectedSchedule = null;

    public function viewSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule) {
            $schedule->incrementViewCount();
            $this->selectedSchedule = $schedule;
        }
    }

    public function downloadSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule) {
            $schedule->incrementDownloadCount();
            return response()->download(
                storage_path('app/public/' . $schedule->file_path),
                $schedule->title . '.' . $schedule->extension
            );
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        $schedules = Schedule::query()
            ->active()
            ->current()
            ->when($this->typeFilter, function($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($user->hasRole('student'), function($query) use ($user) {
                // Filtrer par niveau et parcours de l'étudiant
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