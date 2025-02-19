<?php

namespace App\Livewire\Teacher;

use Carbon\Carbon;
use App\Models\Lesson;
use App\Models\Niveau;
use Livewire\Component;
use App\Models\Semestre;
use App\Models\Programme;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ScheduleTeacher extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Url]
    public $selectedSemestre = '';
    public $selectedProgramme = '';

    public $selectedUe = '';
    public $selectedEc = '';

    #[Url]
    public $selectedTeacher = '';

    #[Url]
    public $selectedParcour = '';

    #[Url]
    public $selectedNiveau = '';
    public $currentDateTime;
    public $typeCours;
    public $timeRange = [
        'start' => '08:00',
        'end' => '18:00'
    ];

    public function mount()
    {
        $this->typeCours = 'CM';
        $this->selectedTeacher = auth()->id();

        if (!$this->selectedNiveau) {
            $niveaux = auth()->user()->niveaux()
                ->where('status', true)
                ->orderBy('sigle')
                ->get();

            if ($niveaux->isNotEmpty()) {
                $firstNiveau = $niveaux->first();
                $this->selectedNiveau = $firstNiveau->id;

                $defaultSemestreName = str_contains(strtolower($firstNiveau->sigle), 'm1') ? 'S1' : 'S3';

                $semestre = Semestre::where('niveau_id', $firstNiveau->id)
                    ->where('name', $defaultSemestreName)
                    ->where('is_active', true)
                    ->where('status', true)
                    ->first();

                if ($semestre) {
                    $this->selectedSemestre = $semestre->id;
                }

                if (!$this->selectedParcour) {
                    $firstParcour = auth()->user()->teacherParcours()
                        ->where('status', true)
                        ->orderBy('sigle')
                        ->first();

                    if ($firstParcour) {
                        $this->selectedParcour = $firstParcour->id;
                    }
                }

                $this->loadProgrammes();
            }
        }
    }

    private function loadProgrammes()
    {
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

    private function getNiveaux()
    {
        return auth()->user()->niveaux()
            ->where('status', true)
            ->orderBy('sigle')
            ->get();
    }

    private function getParcours()
    {
        if (!$this->selectedNiveau) {
            return collect();
        }

        return auth()->user()->teacherParcours()
            ->where('status', true)
            ->whereHas('teachers.niveaux', function($query) {
                $query->where('niveaux.id', $this->selectedNiveau);
            })
            ->orderBy('sigle')
            ->get();
    }

    private function formatTypeCoursName(string $typeCours): string
    {
        return Lesson::TYPES_COURS[$typeCours] ?? $typeCours;
    }

    public function getCalendarData()
    {
        // Récupérer l'ID de l'enseignant connecté
        $teacherId = auth()->id();

        // 1. Récupérer les cours de l'enseignant
        $lessons = Lesson::with(['niveau', 'parcour', 'teacher.profil', 'semestre'])
            ->where('semestre_id', $this->selectedSemestre)
            ->where('teacher_id', auth()->id()) // Filtrer par enseignant connecté
            ->when($this->selectedNiveau, fn($q) => $q->where('niveau_id', $this->selectedNiveau))
            ->when($this->selectedParcour, fn($q) => $q->where('parcour_id', $this->selectedParcour))
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
                $lesson = $lessons->first(function ($lesson) use ($dayNumber, $slot) {
                    return $lesson->weekday == $dayNumber
                        && Carbon::parse($lesson->start_time)->format('H:i') === $slot['start'];
                });

                if ($lesson) {
                    $duration = Carbon::parse($lesson->start_time)
                        ->diffInMinutes(Carbon::parse($lesson->end_time));
                    $rowSpan = $duration / 30;

                    $calendarData[$timeKey][] = [
                        'type' => 'lesson',
                        'id' => $lesson->id,
                        'niveau' => $lesson->niveau->name,
                        'parcour' => $lesson->parcour->name,
                        'salle' => $lesson->salle,
                        'color' => $lesson->color,
                        'type_cours' => $lesson->type_cours,
                        'type_cours_name' => Lesson::TYPES_COURS[$lesson->type_cours] ?? $lesson->type_cours,
                        'description' => $lesson->description,
                        'rowspan' => $rowSpan,
                        'start_time' => Carbon::parse($lesson->start_time)->format('H:i'),
                        'end_time' => Carbon::parse($lesson->end_time)->format('H:i'),
                        'duration' => Carbon::parse($lesson->start_time)
                            ->diffInHours(Carbon::parse($lesson->end_time)) . 'h' .
                            sprintf('%02d', Carbon::parse($lesson->start_time)
                            ->diffInMinutes(Carbon::parse($lesson->end_time)) % 60) . 'min',
                        'weekday' => $dayNumber,
                        'ue' => Programme::where('id', $lesson->programme->parent_id)->first(),
                        'ec' => $lesson->programme
                    ];
                } else {
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
                            'weekday' => $dayNumber
                        ];
                    }
                }
            }
        }

        return [
            'timeSlots' => $timeSlots,
            'calendar' => $calendarData,
            'currentDay' => $this->getCurrentDayName(),
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

    public function getCurrentDayName()
    {
        $today = Carbon::now()->dayOfWeek;
        // Convertir de 0-6 (dimanche = 0) au format 1-6 (lundi-samedi)
        $adjustedDay = $today === 0 ? 6 : $today;
        return Lesson::WEEKDAYS[$adjustedDay] ?? 'Inconnu';
    }

    private function getActiveSemestres()
    {
        if (!$this->selectedNiveau) {
            return collect();
        }

        $niveau = Niveau::find($this->selectedNiveau);
        if (!$niveau) {
            return collect();
        }

        // Si c'est M1, retourner S1 et S2, si M2, retourner S3 et S4
        $semestres = str_contains(strtolower($niveau->sigle), 'm1')
            ? ['S1', 'S2']
            : ['S3', 'S4'];

        return Semestre::where('niveau_id', $this->selectedNiveau)
            ->whereIn('name', $semestres)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();
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

    public function render()
    {
        $calendarData = $this->getCalendarData();
        return view('livewire.teacher.schedule-teacher', [
            'weekDays' => Lesson::WEEKDAYS,
            'semestres' => $this->getActiveSemestres(),
            'calendarData' => $calendarData,
            'niveaux' => $this->getNiveaux(),
            'parcours' => $this->getParcours(),
            'programmes' => $this->getProgrammes(),
            'typesCours' => Lesson::TYPES_COURS
        ]);
    }
}
