<?php

namespace App\Livewire\Admin;

use App\Models\Schedule;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ScheduleManagement extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $typeFilter = '';
    public $niveauFilter = '';
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule) {
            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($schedule->file_path)) {
                Storage::disk('public')->delete($schedule->file_path);
            }
            
            $schedule->delete();
            
            $this->alert('success', 'Supprimé !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Emploi du temps supprimé',
            ]);
        }
    }

    public function toggleStatus($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        
        if ($schedule) {
            $schedule->update(['is_active' => !$schedule->is_active]);
            
            $this->alert('success', 'Modifié !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => $schedule->is_active ? 'Activé' : 'Désactivé',
            ]);
        }
    }

    public function render()
    {
        $schedules = Schedule::query()
            ->with(['niveau', 'parcour', 'semestre', 'uploader'])
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->niveauFilter, function($query) {
                $query->where('niveau_id', $this->niveauFilter);
            })
            ->when($this->statusFilter !== '', function($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.schedule-management', [
            'schedules' => $schedules,
        ]);
    }
}