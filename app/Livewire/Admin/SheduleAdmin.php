<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Niveau;
use App\Models\Parcour;
use Livewire\Component;
use App\Models\Semestre;
use App\Models\Programme;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Notifications\NewSheduleNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SheduleAdmin extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Url]
    public $selectedNiveau = '';

    #[Url]
    public $selectedParcour = '';

    #[Url]
    public $selectedTeacher = '';

    #[Url]
    public $selectedSemestre = '';

    public $selectedUe = '';
    public $selectedEc = '';
    public $selectedProgramme = '';
    public $showCreateModal = false;
    public $weekday;
    public $startTime;
    public $endTime;
    public $startDate;
    public $endDate;
    public $salle;
    public $typeCours;
    public $description;
    public $showDeleteModal = false;
    public $lessonToDelete = null;
    public $lessonToEdit = null;
    public $color = '#2563eb';
    #[Url]
    public $timeRange = [
        'start' => '08:00',
        'end' => '18:00'
    ];


    protected $rules = [
        'selectedNiveau' => 'required',
        'selectedParcour' => 'required',
        'selectedTeacher' => 'required',
        'selectedSemestre' => 'required|exists:semestres,id',
        'weekday' => 'required|integer|between:1,6',
        'startTime' => 'required|date_format:H:i',
        'endTime' => 'required|date_format:H:i|after:startTime',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
        'salle' => 'nullable|string',
        'typeCours' => 'required|in:CM,VC',
        'description' => 'nullable|string',
        'selectedUe' => 'required|exists:programmes,id',
        'selectedProgramme' => 'required|exists:programmes,id',
        'color' => 'required|string|regex:/^#[A-Fa-f0-9]{6}$/',
    ];

    public function mount()
    {
        $this->typeCours = 'CM';
        if ($this->lessonToEdit) {
            $this->typeCours = $this->lessonToEdit->type_cours;
        }
        // Si aucun niveau n'est sélectionné, prendre le premier niveau actif
        if (!$this->selectedNiveau) {
            $firstNiveau = Niveau::where('status', true)
                ->orderBy('sigle')
                ->first();

            if ($firstNiveau) {
                $this->selectedNiveau = $firstNiveau->id;

                // Sélectionner le premier semestre selon le niveau
                $defaultSemestreName = $firstNiveau->sigle === 'M1' ? 'S1' : 'S3';
                $firstSemestre = Semestre::where('niveau_id', $firstNiveau->id)
                    ->where('name', $defaultSemestreName)
                    ->where('is_active', true)
                    ->where('status', true)
                    ->first();

                if ($firstSemestre) {
                    $this->selectedSemestre = $firstSemestre->id;
                }

                // Sélectionner le premier parcours si l'URL n'en contient pas
                if (!$this->selectedParcour) {
                    $firstParcour = Parcour::where('status', true)
                        ->orderBy('sigle')
                        ->first();
                    if ($firstParcour) {
                        $this->selectedParcour = $firstParcour->id;
                    }
                }

                if (!$this->startDate) {
                    $this->startDate = Carbon::now()->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = Carbon::now()->addMonths(4)->format('Y-m-d'); // Durée d'un semestre
                }

                // Après avoir sélectionné niveau, semestre et parcours, on peut charger les UEs
                if ($this->selectedSemestre && $this->selectedParcour) {
                    $firstUE = Programme::where([
                        'type' => 'UE',
                        'niveau_id' => $this->selectedNiveau,
                        'semestre_id' => $this->selectedSemestre,
                        'parcour_id' => $this->selectedParcour,
                        'status' => true
                    ])
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->first();

                    if ($firstUE) {
                        $this->selectedUe = $firstUE->id;

                        // Sélectionner le premier EC de cette UE
                        $firstEC = Programme::where([
                            'type' => 'EC',
                            'parent_id' => $firstUE->id,
                            'status' => true
                        ])
                        ->orderBy('order')
                        ->first();

                        if ($firstEC) {
                            $this->selectedProgramme = $firstEC->id;
                        }
                    }
                }
            }
        }
    }

    public function updatedTypeCours($value)
    {
        // Si c'est une checkbox, $value sera true ou false
        if (is_bool($value)) {
            $this->typeCours = $value ? 'VC' : 'CM';
        } else {
            // Si c'est une valeur directe, vérifier si c'est VC
            $this->typeCours = $value === 'VC' ? 'VC' : 'CM';
        }
    }

    public function updatedSelectedNiveau($value)
    {
        $this->reset([
            'selectedParcour',
            'selectedSemestre',
            'selectedUe',
            'selectedProgramme'
        ]);

        if ($value) {
            $niveau = Niveau::find($value);
            if ($niveau) {
                // Sélectionner automatiquement S1 pour M1 ou S3 pour M2
                $defaultSemestreName = $niveau->sigle === 'M1' ? 'S1' : 'S3';

                $semestre = Semestre::where('niveau_id', $value)
                    ->where('name', $defaultSemestreName)
                    ->where('is_active', true)
                    ->where('status', true)
                    ->first();

                if ($semestre) {
                    $this->selectedSemestre = $semestre->id;
                }
            }
        }
    }

    public function createLesson()
    {
        try {
            $validated = $this->validate();

            if (!in_array($this->typeCours, ['CM', 'VC'])) {
                $this->typeCours = 'CM';
            }
            // Vérifier si le semestre est actif
            $activeSemestres = $this->getActiveSemestres();
            if (!$activeSemestres->contains('id', $this->selectedSemestre)) {
                $this->alert('error', 'Le semestre sélectionné n\'est pas actif.');
                return;
            }

            $programme = Programme::findOrFail($this->selectedProgramme);
            if ($programme->parent_id != $this->selectedUe) {
                throw new \Exception("L'élément constitutif sélectionné n'appartient pas à cette UE.");
            }

            $isAvailable = Lesson::isTimeAvailable(
                $this->weekday,
                $this->startTime,
                $this->endTime,
                $this->selectedNiveau,
                $this->selectedTeacher
            );

            if (!$isAvailable) {
                $this->alert('error', 'Ce créneau horaire n\'est pas disponible.');
                return;
            }

            $lesson = Lesson::create([
                'weekday' => $this->weekday,
                'niveau_id' => $this->selectedNiveau,
                'parcour_id' => $this->selectedParcour,
                'teacher_id' => $this->selectedTeacher,
                'semestre_id' => $this->selectedSemestre,
                'programme_id' => $this->selectedProgramme,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'salle' => $this->salle,
                'color' => $this->color,
                'type_cours' => $this->typeCours,
                'description' => $this->description,
                'is_active' => true
            ]);

            // Envoyer la notification à l'enseignant
            $teacher = User::find($this->selectedTeacher);
            $teacher->notify(new NewSheduleNotification($lesson));

            $this->reset();
            $this->color = '#2563eb';
            $this->showCreateModal = false;
            $this->alert('success', 'Emploi du temps créé avec succès.');
            return redirect()->route('admin.timetable');
            // $this->dispatch('calendar-updated');

        } catch (\Exception $e) {
            $this->alert('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'weekday',
            'startTime',
            'endTime',
            'salle',
            'typeCours',
            'description',
            'showCreateModal',
            'selectedTeacher',
            'selectedParcour',
            'selectedUe',
            'selectedProgramme',
            'lessonToEdit',
            'color',
        ]);
        $this->color = '#2563eb';
        $this->typeCours = 'CM';
        $this->resetErrorBag();
        $this->resetValidation();
        return redirect()->route('admin.timetable');
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    private function getNiveaux()
    {
        return Niveau::where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function updatedSelectedUe($value)
    {
        $this->selectedProgramme = '';
    }

    public function updatedSelectedParcour()
    {
        $this->selectedUe = '';
        $this->selectedProgramme = '';
    }

    public function updatedSelectedSemestre()
    {
        $this->selectedUe = '';
        $this->selectedProgramme = '';
    }

    private function getProgrammes()
    {
        if (!$this->selectedNiveau || !$this->selectedSemestre || !$this->selectedParcour) {
            return [
                'ues' => collect(),
                'ecs' => collect()
            ];
        }

        // Récupérer les UEs avec leurs ECs
        $ues = Programme::where('type', 'UE')
            ->whereNull('parent_id')
            ->where('niveau_id', $this->selectedNiveau)
            ->where('semestre_id', $this->selectedSemestre)
            ->where('parcour_id', $this->selectedParcour)
            ->where('status', true)
            ->orderBy('order')
            ->with(['elements' => function($query) {
                $query->where('status', true)
                      ->where('type', 'EC')
                      ->orderBy('order');
            }])
            ->get();

        return ['ues' => $ues];
    }


    private function getActiveSemestres()
    {
        if (!$this->selectedNiveau) {
            return collect();
        }

        $niveau = Niveau::with('semestres')->find($this->selectedNiveau);
        if (!$niveau) {
            return collect();
        }

        // Si c'est M1, retourner S1 et S2, si M2, retourner S3 et S4
        $semestres = $niveau->sigle === 'M1'
            ? ['S1', 'S2']
            : ['S3', 'S4'];

        return Semestre::where('niveau_id', $this->selectedNiveau)
            ->whereIn('name', $semestres)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    private function getParcours()
    {
        if (!$this->selectedNiveau) {
            return collect();
        }

        if (auth()->user()->hasRole('teacher')) {
            return auth()->user()->teacherParcours;
        }

        return Parcour::whereHas('teachers.niveaux', function($query) {
                $query->where('niveaux.id', $this->selectedNiveau);
            })
            ->where('status', true)
            ->orderBy('sigle')
            ->get();
    }

    private function getTeachers()
    {
        return User::role('teacher')
            ->with('profil')
            ->orderBy('name')
            ->get();
    }

    private function getCalendarData()
    {
        // 1. Récupérer les cours du semestre sélectionné
        $lessons = Lesson::with(['niveau', 'parcour', 'teacher.profil', 'semestre'])
            ->where('semestre_id', $this->selectedSemestre)
            ->when($this->selectedNiveau, fn($q) => $q->where('niveau_id', $this->selectedNiveau))
            ->when($this->selectedParcour, fn($q) => $q->where('parcour_id', $this->selectedParcour))
            ->when($this->selectedTeacher, fn($q) => $q->where('teacher_id', $this->selectedTeacher))
            ->where('is_active', true)
            ->get();

        // 2. Définir les créneaux horaires
        $timeSlots = [];
        $startTime = Carbon::createFromTimeString($this->timeRange['start']);
        $endTime = Carbon::createFromTimeString($this->timeRange['end']);

        while ($startTime < $endTime) {
            $timeSlots[] = [
                'start' => $startTime->format('H:i'),
                'end' => $startTime->copy()->addMinutes(30)->format('H:i')
            ];
            $startTime->addMinutes(30);
        }

        // 3. Construire la grille du calendrier
        $calendarData = [];

        foreach ($timeSlots as $slot) {
            $timeKey = $slot['start'] . ' - ' . $slot['end'];
            $calendarData[$timeKey] = [];

            foreach (Lesson::WEEKDAYS as $dayNumber => $dayName) {
                // Chercher un cours pour ce créneau et ce jour
                $lesson = $lessons->first(function ($lesson) use ($dayNumber, $slot) {
                    return $lesson->weekday == $dayNumber
                        && Carbon::parse($lesson->start_time)->format('H:i') === $slot['start'];
                });

                if ($lesson) {
                    // Calculer le nombre de créneaux occupés par ce cours
                    $duration = Carbon::parse($lesson->start_time)
                        ->diffInMinutes(Carbon::parse($lesson->end_time));
                    $rowSpan = $duration / 30;

                    $calendarData[$timeKey][] = [
                        'id' => $lesson->id,
                        'type' => 'lesson',
                        'weekday' => $dayNumber,
                        'rowspan' => $rowSpan,
                        'type_cours' => $lesson->type_cours,
                        'salle' => $lesson->salle,
                        'color' => $lesson->color,
                        'niveau' => $lesson->niveau->name,
                        'teacher' => $lesson->teacher->getFullNameWithGradeAttribute(),
                        'start_time' => Carbon::parse($lesson->start_time)->format('H:i'),
                        'end_time' => Carbon::parse($lesson->end_time)->format('H:i'),
                        'ue' => Programme::where('id', $lesson->programme->parent_id)->first(),
                        'ec' => $lesson->programme
                    ];
                } else {
                    // Vérifier si ce créneau est déjà occupé par un cours qui a commencé plus tôt
                    $isOccupied = $lessons->contains(function ($lesson) use ($dayNumber, $slot) {
                        return $lesson->weekday == $dayNumber
                            && Carbon::parse($lesson->start_time)->format('H:i') < $slot['start']
                            && Carbon::parse($lesson->end_time)->format('H:i') > $slot['start'];
                    });

                    if (!$isOccupied) {
                        $calendarData[$timeKey][] = [
                            'type' => 'empty',
                            'start' => $slot['start'],
                            'end' => $slot['end'],
                            'weekday' => $dayNumber,
                            'available' => true
                        ];
                    } else {
                        $calendarData[$timeKey][] = [
                            'type' => 'occupied',
                            'available' => false
                        ];
                    }
                }
            }
        }

        // Récupérer les informations du semestre actuel
        $currentSemestre = Semestre::find($this->selectedSemestre);

        return [
            'timeSlots' => $timeSlots,
            'calendar' => $calendarData,
            'currentSemestre' => $currentSemestre ? $currentSemestre->name : '',
            'summary' => [
                'total_lessons' => $lessons->count(),
                'total_hours' => $lessons->sum(function ($lesson) {
                    return Carbon::parse($lesson->start_time)
                        ->diffInHours(Carbon::parse($lesson->end_time));
                }),
                'lessons_by_type' => $lessons->groupBy('type_cours')
                    ->map(fn($group) => $group->count()),
            ]
        ];
    }

    public function confirmDelete($lessonId)
    {
        $this->lessonToDelete = Lesson::find($lessonId);
        $this->showDeleteModal = true;
    }

    public function deleteLesson()
    {
        if ($this->lessonToDelete) {
            $this->lessonToDelete->delete();
            $this->showDeleteModal = false;
            $this->lessonToDelete = null;
            $this->alert('success', 'Cours supprimé avec succès.');
        }
    }

    public function editLesson($lessonId)
    {
        // Utilisez le chargement différé avec load() pour les relations
        $lesson = Lesson::find($lessonId);

        if (!$lesson) {
            $this->alert('error', 'Cours non trouvé.');
            return;
        }

        // Chargez les relations nécessaires en une seule requête
        $lesson = Lesson::with(['programme.parent'])->find($lessonId);

        // Utilisez des propriétés publiques pour le stockage temporaire
        $this->lessonToEdit = $lesson;

        // Mise à jour des champs en une seule fois
        $this->fill([
            'selectedNiveau' => $lesson->niveau_id,
            'selectedParcour' => $lesson->parcour_id,
            'selectedTeacher' => $lesson->teacher_id,
            'selectedSemestre' => $lesson->semestre_id,
            'color' => $lesson->color,
            'weekday' => $lesson->weekday,
            'startTime' => Carbon::parse($lesson->start_time)->format('H:i'),
            'endTime' => Carbon::parse($lesson->end_time)->format('H:i'),
            'startDate' => Carbon::parse($lesson->start_date)->format('Y-m-d'),
            'endDate' => Carbon::parse($lesson->end_date)->format('Y-m-d'),
            'salle' => $lesson->salle,
            'typeCours' => $lesson->type_cours,
            'description' => $lesson->description
        ]);

        // Mettre à jour UE et EC si disponibles
        if ($lesson->programme) {
            $this->selectedUe = $lesson->programme->parent_id;
            $this->selectedProgramme = $lesson->programme_id;
        }

        // Différer l'affichage du modal
        $this->showCreateModal = true;
    }

    // Méthode pour mettre à jour un cours
    public function updateLesson()
    {
        try {
            $validatedData = $this->validate();

            if (!in_array($this->typeCours, ['CM', 'VC'])) {
                $this->typeCours = 'CM';
            }

            if (!$this->lessonToEdit) {
                throw new \Exception('Aucun cours à mettre à jour.');
            }

            // Vérifier si le semestre est actif
            $activeSemestres = $this->getActiveSemestres();
            if (!$activeSemestres->contains('id', $this->selectedSemestre)) {
                $this->alert('error', 'Le semestre sélectionné n\'est pas actif.');
                return;
            }

            $programme = Programme::findOrFail($this->selectedProgramme);
            if ($programme->parent_id != $this->selectedUe) {
                throw new \Exception("L'élément constitutif sélectionné n'appartient pas à cette UE.");
            }

            // Vérifier la disponibilité du créneau
            $isAvailable = Lesson::isTimeAvailable(
                $this->weekday,
                $this->startTime,
                $this->endTime,
                $this->selectedNiveau,
                $this->selectedTeacher,
                $this->lessonToEdit->id
            );

            if (!$isAvailable) {
                $this->alert('error', 'Ce créneau horaire n\'est pas disponible.');
                return;
            }

            $this->lessonToEdit->update([
                'niveau_id' => $this->selectedNiveau,
                'parcour_id' => $this->selectedParcour,
                'teacher_id' => $this->selectedTeacher,
                'semestre_id' => $this->selectedSemestre,
                'programme_id' => $this->selectedProgramme,
                'weekday' => $this->weekday,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'salle' => $this->salle,
                'color' => $this->color,
                'type_cours' => $this->typeCours,
                'description' => $this->description
            ]);

            $this->reset();
            $this->showCreateModal = false;
            $this->alert('success', 'Cours mis à jour avec succès.');
            return redirect()->route('admin.timetable');

        } catch (\Exception $e) {
            $this->alert('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.calendar', [
            'weekDays' => Lesson::WEEKDAYS,
            'semestres' => $this->getActiveSemestres(),
            'calendarData' => $this->getCalendarData(),
            'niveaux' => Niveau::where('status', true)
                ->orderBy('sigle')
                ->get(),
            'parcours' => $this->getParcours(),
            'teachers' => $this->getTeachers(),
            'typesCours' => Lesson::TYPES_COURS,
            'programmes' => $this->getProgrammes()
        ]);
    }
}
