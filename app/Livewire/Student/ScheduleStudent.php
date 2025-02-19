<?php

namespace App\Livewire\Student;

use Carbon\Carbon;
use App\Models\Lesson;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use App\Models\Programme;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ScheduleStudent extends Component
{
    use WithPagination;
    use LivewireAlert;

    #[Url]
    public $selectedSemestre = '';

    public $selectedUe = '';
    public $selectedEc = '';
    public $currentDateTime;
    public $niveau;
    public $parcour;
    public $typeCours;
    public $timeRange = [
        'start' => '08:00',
        'end' => '18:00'
    ];

    public function mount()
    {
        $this->typeCours = 'CM';
        $this->currentDateTime = now()->format('Y-m-d H:i:s');

        // Récupérer l'étudiant connecté
        $student = auth()->user();
        $this->niveau = $student->niveau_id;
        $this->parcour = $student->parcour_id;

        // Récupérer le semestre actif par défaut
        if (!$this->selectedSemestre) {
            $niveau = Niveau::find($this->niveau);

            if ($niveau) {
                $defaultSemestreName = str_contains(strtolower($niveau->sigle), 'm1') ? 'S1' : 'S3';

                $semestre = Semestre::where('niveau_id', $this->niveau)
                    ->where('name', $defaultSemestreName)
                    ->where('is_active', true)
                    ->where('status', true)
                    ->first();

                if ($semestre) {
                    $this->selectedSemestre = $semestre->id;
                }
            }
        }

        $this->loadProgrammes();
    }

    private function loadProgrammes()
    {
        if ($this->selectedSemestre && $this->parcour) {
            $firstUE = Programme::where([
                'type' => 'UE',
                'niveau_id' => $this->niveau,
                'semestre_id' => $this->selectedSemestre,
                'parcour_id' => $this->parcour,
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
                    $this->selectedEc = $firstEC->id;
                }
            }
        }
    }

    private function formatTypeCoursName(string $typeCours): string
    {
        return Lesson::TYPES_COURS[$typeCours] ?? $typeCours;
    }

    public function getCalendarData()
    {
        // 1. Récupérer les cours de la classe de l'étudiant
        $lessons = Lesson::with(['teacher.profil', 'niveau', 'parcour', 'semestre', 'programme'])
            ->where('niveau_id', $this->niveau)
            ->where('parcour_id', $this->parcour)
            ->where('semestre_id', $this->selectedSemestre)
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
                        'teacher' => $lesson->teacher->getFullNameWithGradeAttribute(),
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
                        'niveau' => $lesson->niveau->name,
                        'parcour' => $lesson->parcour->name,
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

        // Récupérer les informations du niveau et du parcours
        $currentNiveau = Niveau::find($this->niveau);
        $currentParcour = Parcour::find($this->parcour);

        return [
            'timeSlots' => $timeSlots,
            'calendar' => $calendarData,
            'currentNiveau' => $currentNiveau ? $currentNiveau->name : '',
            'currentParcour' => $currentParcour ? $currentParcour->name : '',
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
        if (!$this->niveau) {
            return collect();
        }

        $niveau = Niveau::find($this->niveau);
        if (!$niveau) {
            return collect();
        }

        // Si c'est M1, retourner S1 et S2, si M2, retourner S3 et S4
        $semestres = str_contains(strtolower($niveau->sigle), 'm1')
            ? ['S1', 'S2']
            : ['S3', 'S4'];

        return Semestre::where('niveau_id', $this->niveau)
            ->whereIn('name', $semestres)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    private function getProgrammes()
    {
        if (!$this->selectedSemestre) {
            return [
                'ues' => collect(),
                'ecs' => collect()
            ];
        }

        // Récupérer les UEs avec leurs ECs
        $ues = Programme::where('type', 'UE')
            ->whereNull('parent_id')
            ->where('niveau_id', $this->niveau)
            ->where('semestre_id', $this->selectedSemestre)
            ->where('parcour_id', $this->parcour)
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
        $currentDay = $this->getCurrentDayName();

        return view('livewire.student.schedule-student', [
            'weekDays' => Lesson::WEEKDAYS,
            'semestres' => $this->getActiveSemestres(),
            'calendarData' => $calendarData,
            'programmes' => $this->getProgrammes(),
            'typesCours' => Lesson::TYPES_COURS,
            'currentDay' => $currentDay,
            'currentDateTime' => $this->currentDateTime
        ]);
    }
}
