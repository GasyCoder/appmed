<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_UE = 'UE';
    const TYPE_EC = 'EC';

    protected $fillable = [
        'type',
        'code',
        'name',
        'order',
        'parent_id',
        'semestre_id',
        'niveau_id',
        'parcour_id',
        'credits',
        'coefficient',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'credits' => 'integer',
        'coefficient' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function elements()
    {
        return $this->hasMany(Programme::class, 'parent_id')
            ->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Programme::class, 'parent_id');
    }

    public function enseignant()
    {
        return $this->belongsToMany(User::class, 'programme_user')
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'is_responsable', 'note'])
            ->withTimestamps()
            ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
            ->first();
    }

    public function enseignants()
    {
        return $this->belongsToMany(User::class, 'programme_user')
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'is_responsable', 'note'])
            ->withTimestamps()
            ->whereHas('roles', fn($q) => $q->where('name', 'teacher'));
    }

    public function responsable()
    {
        return $this->belongsToMany(User::class, 'programme_user')
            ->wherePivot('is_responsable', true)
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'note'])
            ->withTimestamps()
            ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
            ->first();
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function parcour()
    {
        return $this->belongsTo(Parcour::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUes($query)
    {
        return $query->whereNull('parent_id')
            ->where('type', self::TYPE_UE);
    }

    public function scopeEcs($query)
    {
        return $query->whereNotNull('parent_id')
            ->where('type', self::TYPE_EC);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBySemestre($query, $semestreId)
    {
        return $query->where('semestre_id', $semestreId);
    }

    public function scopeByNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    public function scopeByParcours($query, $parcourId)
    {
        return $query->where('parcour_id', $parcourId);
    }

    public function scopeByAnnee($query, $annee)
    {
        if ($annee == 4) return $query->whereIn('semestre_id', [1, 2]);
        if ($annee == 5) return $query->whereIn('semestre_id', [3, 4]);
        return $query;
    }

    public function scopeWithEnseignants($query)
    {
        return $query->whereHas('enseignants');
    }

    public function scopeWithoutEnseignants($query)
    {
        return $query->whereDoesntHave('enseignants');
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ Scopes “Étudiant” (M1/M2 + Parcours)
    |--------------------------------------------------------------------------
    | Règles:
    | - Étudiant M1 => Semestre 1-2 uniquement + (niveau_id si rempli) + parcour_id
    | - Étudiant M2 => Semestre 3-4 uniquement + (niveau_id si rempli) + parcour_id
    */

    public static function semestresForM1(): array
    {
        return [1, 2];
    }

    public static function semestresForM2(): array
    {
        return [3, 4];
    }

    /**
     * Filtre générique selon user (niveau/parcours + mapping semestre).
     * - Si niveau_id existe dans programmes => on filtre aussi par niveau_id.
     * - Sinon, le mapping semestre suffit (S1-2 vs S3-4).
     */
    public function scopeVisibleForStudent($query, User $student)
    {
        $query->where('status', true);

        // Parcours (tu dis "un seul parcours", mais on garde la sécurité)
        if (!empty($student->parcour_id)) {
            $query->where('parcour_id', $student->parcour_id);
        }

        // Filtre niveau si programmes stockent bien niveau_id
        if (!empty($student->niveau_id)) {
            $query->where('niveau_id', $student->niveau_id);
        } else {
            // Fallback basé sur semestre si student.niveau_id est absent
            // (rare, mais safe)
            $query->whereIn('semestre_id', self::semestresForM1()); // défaut M1
        }

        // Anti-erreur: si un admin a mal rempli niveau_id mais semestre indique M1/M2,
        // on renforce via mapping semestre
        // -> on déduit M1/M2 à partir des semestres autorisés pour le niveau de l'étudiant
        // Si ton user a toujours niveau_id, ce bloc est suffisant:
        if (!empty($student->niveau_id) && $student->relationLoaded('niveau') && $student->niveau) {
            $sigle = strtoupper((string) ($student->niveau->sigle ?? $student->niveau->name ?? ''));
            if ($sigle === 'M1') {
                $query->whereIn('semestre_id', self::semestresForM1());
            } elseif ($sigle === 'M2') {
                $query->whereIn('semestre_id', self::semestresForM2());
            }
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes Utilitaires
    |--------------------------------------------------------------------------
    */

    public function isUe(): bool { return $this->type === self::TYPE_UE; }
    public function isEc(): bool { return $this->type === self::TYPE_EC; }

    public function hasElements(): bool
    {
        return $this->elements()->count() > 0;
    }

    public function getFullName(): string
    {
        return "{$this->code} - {$this->name}";
    }

    public function getAnneeAttribute(): int
    {
        return in_array($this->semestre_id, [1, 2]) ? 4 : 5;
    }

    public function getSemestreAnneeAttribute(): int
    {
        return in_array($this->semestre_id, [1, 3]) ? 1 : 2;
    }

    public function getTotalHeures(): int
    {
        if ($this->isUe()) {
            return $this->elements->sum(fn($ec) => $ec->getTotalHeures());
        }

        $enseignant = $this->enseignants->first();
        if (!$enseignant) return 0;

        return (int) $enseignant->pivot->heures_cm
            + (int) $enseignant->pivot->heures_td
            + (int) $enseignant->pivot->heures_tp;
    }

    public function getHeuresDetail(): array
    {
        if ($this->isUe()) {
            $cm = $td = $tp = 0;
            foreach ($this->elements as $ec) {
                $detail = $ec->getHeuresDetail();
                $cm += (int) $detail['cm'];
                $td += (int) $detail['td'];
                $tp += (int) $detail['tp'];
            }
            return compact('cm', 'td', 'tp');
        }

        $enseignant = $this->enseignants->first();
        if (!$enseignant) return ['cm' => 0, 'td' => 0, 'tp' => 0];

        return [
            'cm' => (int) $enseignant->pivot->heures_cm,
            'td' => (int) $enseignant->pivot->heures_td,
            'tp' => (int) $enseignant->pivot->heures_tp,
        ];
    }

    public function getTotalCredits(): int
    {
        if ($this->isUe()) return (int) $this->elements->sum('credits');
        return (int) ($this->credits ?? 0);
    }

    public function assignerEnseignant(
        User $user,
        int $heuresCm = 0,
        int $heuresTd = 0,
        int $heuresTp = 0,
        ?string $note = null
    ): void {
        if (!$this->isEc()) throw new \Exception('Seuls les ECs peuvent avoir un enseignant assigné.');
        if (!$user->hasRole('teacher')) throw new \Exception('L\'utilisateur doit avoir le rôle "teacher".');

        $this->enseignants()->sync([
            $user->id => [
                'heures_cm' => $heuresCm,
                'heures_td' => $heuresTd,
                'heures_tp' => $heuresTp,
                'is_responsable' => true,
                'note' => $note,
            ]
        ]);
    }

    public function retirerEnseignant(): void
    {
        $this->enseignants()->detach();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($programme) {
            if (!$programme->order) {
                $maxOrder = static::where('parent_id', $programme->parent_id)
                    ->where('type', $programme->type)
                    ->where('semestre_id', $programme->semestre_id)
                    ->max('order');
                $programme->order = ($maxOrder ?? 0) + 1;
            }
        });

        static::saving(function ($programme) {
            if ($programme->type === self::TYPE_EC && !$programme->parent_id) {
                throw new \Exception('Un EC doit avoir une UE parente.');
            }
            if ($programme->type === self::TYPE_UE && $programme->parent_id) {
                throw new \Exception('Une UE ne peut pas avoir de parent.');
            }
        });

        static::deleting(function ($programme) {
            $programme->enseignants()->detach();
        });
    }
}
