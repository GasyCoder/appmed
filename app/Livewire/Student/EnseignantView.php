<?php

namespace App\Livewire\Student;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class EnseignantView extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedTeacher = null;
    public $showTeacherModal = false;

    protected $listeners = ['closeModal' => 'closeTeacherModal'];

    public function mount()
    {
        abort_if(!auth()->user()->hasRole('student'), 403);

        if (!auth()->user()->niveau_id || !auth()->user()->parcour_id) {
            return redirect()->route('profile.show')
                ->with('error', 'Veuillez compléter votre profil.');
        }
    }

    public function getTeachersProperty()
    {
        $studentNiveauId = auth()->user()->niveau_id;
        $studentParcourId = auth()->user()->parcour_id;

        return User::query()
            ->role('teacher')
            ->where('status', true)
            ->whereHas('teacherNiveaux', function($query) use ($studentNiveauId) {
                $query->where('niveau_id', $studentNiveauId);
            })
            ->withCount(['documents' => function($query) use ($studentNiveauId, $studentParcourId) {
                $query->where('is_actif', true)
                      ->where('niveau_id', $studentNiveauId)
                      ->where('parcour_id', $studentParcourId);
            }])
            ->with(['teacherNiveaux', 'teacherParcours', 'profil'])
            ->when($this->search, function($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->get();
    }

    public function showTeacherProfile($teacherId)
    {
        $studentNiveauId = auth()->user()->niveau_id;
        $studentParcourId = auth()->user()->parcour_id;

        $this->selectedTeacher = User::query()
            ->withCount(['documents' => function($query) use ($studentNiveauId, $studentParcourId) {
                $query->where('is_actif', true)
                      ->where('niveau_id', $studentNiveauId)
                      ->where('parcour_id', $studentParcourId);
            }])
            ->with(['profil', 'teacherNiveaux', 'teacherParcours'])
            ->findOrFail($teacherId);

        $this->showTeacherModal = true;
    }

    public function closeTeacherModal()
    {
        $this->showTeacherModal = false;
        $this->selectedTeacher = null;
    }

    public function render()
    {
        return view('livewire.student.enseignant-view', [
            'teachers' => $this->teachers
        ]);
    }
}
