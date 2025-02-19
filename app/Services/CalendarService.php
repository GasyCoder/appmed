<?php

namespace App\Services;

use App\Models\Lesson;
use Carbon\Carbon;

class CalendarService
{
    private const TIME_SLOT_DURATION = 30; // en minutes

    public function generateCalendarData($startTime = '08:00', $endTime = '18:00')
    {
        $calendarData = [];
        $timeSlots = $this->generateTimeSlots($startTime, $endTime);

        $lessons = Lesson::with(['niveau', 'parcour', 'teacher.profil'])
            ->calendarByRole()
            ->active()
            ->get();

        foreach ($timeSlots as $timeSlot) {
            $timeText = $timeSlot['start'] . ' - ' . $timeSlot['end'];
            $calendarData[$timeText] = [];

            foreach (Lesson::WEEKDAYS as $dayIndex => $dayName) {
                $lesson = $lessons->where('weekday', $dayIndex)
                    ->where('start_time', Carbon::parse($timeSlot['start']))
                    ->first();

                if ($lesson) {
                    $calendarData[$timeText][] = [
                        'lesson_id' => $lesson->id,
                        'niveau' => $lesson->niveau->nom,
                        'parcour' => $lesson->parcour->name,
                        'teacher' => $lesson->teacher->getFullNameWithGradeAttribute(),
                        'salle' => $lesson->salle,
                        'type_cours' => $lesson->type_cours_name,
                        'rowspan' => $lesson->duration / self::TIME_SLOT_DURATION,
                        'description' => $lesson->description
                    ];
                }
                // Vérifie si le créneau est déjà occupé par un cours qui a commencé plus tôt
                elseif (!$lessons->where('weekday', $dayIndex)
                    ->where('start_time', '<', $timeSlot['start'])
                    ->where('end_time', '>', $timeSlot['end'])
                    ->count()) {
                    $calendarData[$timeText][] = [
                        'available' => true,
                        'start' => $timeSlot['start'],
                        'end' => $timeSlot['end'],
                        'weekday' => $dayIndex
                    ];
                } else {
                    $calendarData[$timeText][] = [
                        'available' => false
                    ];
                }
            }
        }

        return $calendarData;
    }

    private function generateTimeSlots($startTime, $endTime): array
    {
        $timeSlots = [];
        $currentTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);

        while ($currentTime < $endTime) {
            $timeSlots[] = [
                'start' => $currentTime->format('H:i'),
                'end' => $currentTime->addMinutes(self::TIME_SLOT_DURATION)->format('H:i')
            ];
        }

        return $timeSlots;
    }

    public function getAvailableRooms($weekday, $startTime, $endTime)
    {
        // Liste des salles occupées pendant ce créneau
        $occupiedRooms = Lesson::where('weekday', $weekday)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->where('is_active', true)
            ->pluck('salle')
            ->toArray();

        // Retourne les salles disponibles (à implémenter selon vos besoins)
        return array_diff($this->getAllRooms(), $occupiedRooms);
    }

    private function getAllRooms(): array
    {
        // À personnaliser selon vos besoins
        return [
            'Amphi A',
            'Amphi B',
            'Salle 101',
            'Salle 102',
            'Salle TP 1',
            'Salle TP 2',
            // Ajoutez vos salles ici
        ];
    }

    public function getTeacherSchedule($teacherId, $startDate = null, $endDate = null)
    {
        $query = Lesson::with(['niveau', 'parcour'])
            ->forTeacher($teacherId)
            ->active();

        if ($startDate && $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->orderBy('weekday')
                    ->orderBy('start_time')
                    ->get();
    }

    public function getNiveauSchedule($niveauId, $parcourId = null)
    {
        $query = Lesson::with(['teacher.profil'])
            ->forNiveau($niveauId)
            ->active();

        if ($parcourId) {
            $query->forParcour($parcourId);
        }

        return $query->orderBy('weekday')
                    ->orderBy('start_time')
                    ->get();
    }
}
