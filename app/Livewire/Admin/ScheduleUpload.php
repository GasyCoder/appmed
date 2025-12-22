<?php

namespace App\Livewire\Admin;

use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use App\Models\Schedule;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ScheduleUpload extends Component
{
    use WithFileUploads, LivewireAlert;

    public $file;
    public $title = '';
    public $type = 'emploi_du_temps';
    public $academic_year = '';
    public $niveau_id = '';
    public $parcour_id = '';
    public $semestre_id = '';
    public $start_date = '';
    public $end_date = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'title' => 'required|string|min:3|max:255',
            'type' => 'required|in:emploi_du_temps,planning_examens,calendrier',
            'academic_year' => 'required|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'parcour_id' => 'nullable|exists:parcours,id',
            'semestre_id' => 'nullable|exists:semestres,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        // Année académique par défaut
        $this->academic_year = $this->getCurrentAcademicYear();
    }

    private function getCurrentAcademicYear()
    {
        $year = now()->year;
        $month = now()->month;
        
        // Si on est entre septembre et décembre, c'est l'année N/N+1
        if ($month >= 9) {
            return $year . '-' . ($year + 1);
        }
        // Sinon c'est l'année N-1/N
        return ($year - 1) . '-' . $year;
    }

    public function uploadSchedule()
    {
        $this->validate();

        try {
            $extension = $this->file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug($this->title) . '_' . Str::random(6) . '.' . $extension;
            $filePath = $this->file->storeAs('schedules', $fileName, 'public');
            
            $absolutePath = storage_path('app/public/' . $filePath);

            Schedule::create([
                'title' => $this->title,
                'file_path' => $filePath,
                'file_type' => $extension,
                'file_size' => filesize($absolutePath),
                'academic_year' => $this->academic_year,
                'type' => $this->type,
                'niveau_id' => $this->niveau_id ?: null,
                'parcour_id' => $this->parcour_id ?: null,
                'semestre_id' => $this->semestre_id ?: null,
                'start_date' => $this->start_date ?: null,
                'end_date' => $this->end_date ?: null,
                'is_active' => $this->is_active,
                'uploaded_by' => Auth::id(),
            ]);

            $this->alert('success', 'Succès !', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Emploi du temps uploadé avec succès',
            ]);

            return redirect()->route('admin.timetable');

        } catch (\Exception $e) {
            $this->alert('error', 'Erreur', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.schedule-upload', [
            'niveaux' => Niveau::where('status', true)->orderBy('name')->get(),
            'parcours' => Parcour::where('status', true)->orderBy('name')->get(),
            'semestres' => Semestre::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}