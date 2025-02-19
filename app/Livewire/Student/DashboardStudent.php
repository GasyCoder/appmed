<?php

namespace App\Livewire\Student;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Lesson;
use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DashboardStudent extends Component
{
    public function getCurrentDateTimeProperty()
    {
        return Carbon::now();
    }

    public function getUserSessionsProperty()
    {
        return DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->take(5)
            ->get()
            ->map(function ($session) {
                return (object)[
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity)
                ];
            });
    }

    public function getLastLoginProperty()
    {
        return DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->first();
    }

    public function getTodayLessonsProperty()
    {
        $student = auth()->user();
        $today = Carbon::now()->dayOfWeek;
        $today = $today === 0 ? 6 : $today;

        return Lesson::with(['teacher.profil'])
            ->calendarByRole()
            ->where('weekday', $today)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    public function getUpcomingLessonsProperty()
    {
        $student = auth()->user();
        $today = Carbon::now()->dayOfWeek;
        $today = $today === 0 ? 6 : $today;

        return Lesson::with(['teacher.profil'])
            ->calendarByRole()
            ->where('is_active', true)
            ->where(function($query) use ($today) {
                $query->where('weekday', '>', $today)
                      ->orWhere('weekday', '<', $today);
            })
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->take(3) // Limite aux 3 prochains cours
            ->get()
            ->map(function($lesson) {
                $lesson->day_name = Lesson::WEEKDAYS[$lesson->weekday];
                return $lesson;
            });
    }


    public function debugLessonsData()
    {
        $student = auth()->user();
        $today = Carbon::now()->dayOfWeek;
        $today = $today === 0 ? 6 : $today;

        // Récupérer tous les cours (sans filtre de jour) pour debug
        $allLessons = Lesson::where('niveau_id', $student->niveau_id)
            ->where('parcour_id', $student->parcour_id)
            ->where('is_active', true)
            ->get();

        dd([
            'student_info' => [
                'id' => $student->id,
                'niveau_id' => $student->niveau_id,
                'parcour_id' => $student->parcour_id,
            ],
            'today' => [
                'day_number' => $today,
                'day_name' => Lesson::WEEKDAYS[$today] ?? 'Unknown'
            ],
            'all_lessons_count' => $allLessons->count(),
            'all_lessons' => $allLessons->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'weekday' => [
                        'number' => $lesson->weekday,
                        'name' => $lesson->weekday_name
                    ],
                    'time' => [
                        'start' => $lesson->start_time->format('H:i'),
                        'end' => $lesson->end_time->format('H:i')
                    ],
                    'type' => $lesson->type_cours,
                    'salle' => $lesson->salle
                ];
            })
        ]);
    }

    public function formatTime($time)
    {
        return Carbon::parse($time)->format('H:i');
    }

    public function getRecentDocumentsProperty()
    {
        return Document::query()
            ->where('is_actif', true)
            ->where('niveau_id', auth()->user()->niveau_id)
            ->where('parcour_id', auth()->user()->parcour_id)
            ->with('uploader.profil')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getTeachersProperty()
    {
        return User::query()
            ->role('teacher')
            ->whereHas('teacherNiveaux', function($query) {
                $query->where('niveau_id', auth()->user()->niveau_id);
            })
            ->with(['profil', 'teacherNiveaux'])
            ->take(4)
            ->get();
    }

    public function getCurrentDayName()
    {
        $today = Carbon::now()->dayOfWeek;
        // Convertir de 0-6 (dimanche = 0) au format 1-6 (lundi-samedi) utilisé dans votre modèle
        $adjustedDay = $today === 0 ? 6 : $today;
        return Lesson::WEEKDAYS[$adjustedDay] ?? 'Inconnu';
    }

    public function render()
    {
        $lastLogin = $this->lastLogin ? Carbon::createFromTimestamp($this->lastLogin->last_activity) : null;
        $todayLessons = $this->todayLessons;

        return view('livewire.student.dashboard-student', [
            'todayLessons' => $todayLessons,
            'upcomingLessons' => $this->upcomingLessons,
            'recentDocuments' => $this->recentDocuments,
            'currentDayName' => $this->getCurrentDayName(),
            'teachers' => $this->teachers,
            'student' => auth()->user()->load(['niveau', 'parcour']),
            'currentDateTime' => $this->currentDateTime,
            'userSessions' => $this->userSessions,
            'lastLogin' => $lastLogin
        ]);

    }
}

