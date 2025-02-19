<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'niveau_id',
        'parcour_id',
        'teacher_id',
        'semestre_id', // Ajouté
        'programme_id',
        'weekday',
        'start_time',
        'end_time',
        'salle',
        'type_cours',
        'description',
        'is_active',
        'color',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const WEEKDAYS = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
    ];

    const PERIODS = [
        'S1' => 'Semestre 1',
        'S2' => 'Semestre 2'
    ];

    const WEEKS = [
        1 => 'Semaine 1',
        2 => 'Semaine 2',
        3 => 'Semaine 3',
        4 => 'Semaine 4',
        5 => 'Semaine 5',
        6 => 'Semaine 6',
        7 => 'Semaine 7',
        8 => 'Semaine 8',
        9 => 'Semaine 9',
        10 => 'Semaine 10',
        11 => 'Semaine 11',
        12 => 'Semaine 12',
        13 => 'Semaine 13',
        14 => 'Semaine 14',
        15 => 'Semaine 15',
    ];

    const TYPES_COURS = [
        'CM' => 'Cours Magistral',
        'VC' => 'Visio Conférence'
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }


    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function parcour(): BelongsTo
    {
        return $this->belongsTo(Parcour::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function getDurationAttribute(): int
    {
        return Carbon::parse($this->end_time)->diffInMinutes(Carbon::parse($this->start_time));
    }

    public function getWeekdayNameAttribute(): string
    {
        return self::WEEKDAYS[$this->weekday] ?? '';
    }

    public function getTypeCoursNameAttribute(): string
    {
        return self::TYPES_COURS[$this->type_cours] ?? $this->type_cours;
    }

    public static function isTimeAvailable($weekday, $startTime, $endTime, $niveauId, $teacherId, $excludeLessonId = null)
    {
        $query = self::where('weekday', $weekday)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->where(function($query) use ($niveauId, $teacherId) {
                $query->where('niveau_id', $niveauId)
                      ->orWhere('teacher_id', $teacherId);
            })
            ->where('is_active', true);

        // Exclure le cours en cours de modification
        if ($excludeLessonId) {
            $query->where('id', '!=', $excludeLessonId);
        }

        return $query->count() === 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    public function scopeForParcour($query, $parcourId)
    {
        return $query->where('parcour_id', $parcourId);
    }

    public function scopeCalendarByRole($query)
    {
        if (auth()->user()->hasRole('teacher')) {
            return $query->forTeacher(auth()->id());
        }

        if (auth()->user()->hasRole('student')) {
            return $query->forNiveau(auth()->user()->niveau_id)
                        ->forParcour(auth()->user()->parcour_id);
        }

        return $query;
    }
}

