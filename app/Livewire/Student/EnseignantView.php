<?php

namespace App\Livewire\Student;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EnseignantView extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedTeacher = null;
    public $showTeacherModal = false;

    protected $listeners = ['closeModal' => 'closeTeacherModal'];

    // Important pour que la pagination reset quand on tape dans search
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        abort_if(!Auth::user()->hasRole('student'), 403);

        if (!Auth::user()->niveau_id || !Auth::user()->parcour_id) {
            return redirect()->route('profile.show')
                ->with('error', 'Veuillez compléter votre profil.');
        }
    }

    protected function baseTeachersQuery()
    {
        $studentNiveauId = Auth::user()->niveau_id;
        $studentParcourId = Auth::user()->parcour_id;

        return User::query()
            ->role('teacher')
            ->where('status', true)
            ->whereHas('teacherNiveaux', function ($q) use ($studentNiveauId) {
                $q->where('niveau_id', $studentNiveauId);
            })
            ->withCount(['documents' => function ($q) use ($studentNiveauId, $studentParcourId) {
                $q->where('is_actif', true)
                  ->where('niveau_id', $studentNiveauId)
                  ->where('parcour_id', $studentParcourId);
            }])
            ->with(['teacherNiveaux', 'teacherParcours', 'profil'])
            ->when($this->search, function ($q) {
                $s = trim($this->search);
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                       ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->orderBy('name');
    }

    public function getTeachersProperty()
    {
        return $this->baseTeachersQuery()->paginate(9);
    }

    public function showTeacherProfile($teacherId)
    {
        $studentNiveauId = Auth::user()->niveau_id;
        $studentParcourId = Auth::user()->parcour_id;

        $this->selectedTeacher = User::query()
            ->withCount(['documents' => function ($q) use ($studentNiveauId, $studentParcourId) {
                $q->where('is_actif', true)
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
            'teachers' => $this->teachers,
        ]);
    }
}
