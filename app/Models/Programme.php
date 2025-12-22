<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    // Types de programme
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

    /**
     * Les éléments constitutifs (EC) d'une UE
     */
    public function elements()
    {
        return $this->hasMany(Programme::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * L'unité d'enseignement parente (pour un EC)
     */
    public function parent()
    {
        return $this->belongsTo(Programme::class, 'parent_id');
    }

    /**
     * Les enseignants assignés à ce programme (EC)
     * Uniquement les users avec le rôle 'teacher'
     */
    public function enseignants()
    {
        return $this->belongsToMany(User::class, 'programme_user')
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'is_responsable', 'note'])
            ->withTimestamps()
            ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
            ->orderByPivot('is_responsable', 'desc');
    }

    /**
     * L'enseignant responsable du programme
     */
    public function responsable()
    {
        return $this->belongsToMany(User::class, 'programme_user')
            ->wherePivot('is_responsable', true)
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'note'])
            ->withTimestamps()
            ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
            ->first();
    }

    /**
     * Le semestre
     */
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Le niveau (M1, M2, etc.)
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Le parcours
     */
    public function parcour()
    {
        return $this->belongsTo(Parcour::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope pour les UEs uniquement
     */
    public function scopeUes($query)
    {
        return $query->whereNull('parent_id')
            ->where('type', self::TYPE_UE);
    }

    /**
     * Scope pour les ECs uniquement
     */
    public function scopeEcs($query)
    {
        return $query->whereNotNull('parent_id')
            ->where('type', self::TYPE_EC);
    }

    /**
     * Scope pour les programmes actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope par semestre
     */
    public function scopeBySemestre($query, $semestreId)
    {
        return $query->where('semestre_id', $semestreId);
    }

    /**
     * Scope par niveau
     */
    public function scopeByNiveau($query, $niveauId)
    {
        return $query->where('niveau_id', $niveauId);
    }

    /**
     * Scope par parcours
     */
    public function scopeByParcours($query, $parcourId)
    {
        return $query->where('parcour_id', $parcourId);
    }

    /**
     * Scope par année (4ème ou 5ème)
     * 4ème année : Semestre 1 et 2
     * 5ème année : Semestre 3 et 4
     */
    public function scopeByAnnee($query, $annee)
    {
        if ($annee == 4) {
            return $query->whereIn('semestre_id', [1, 2]);
        } elseif ($annee == 5) {
            return $query->whereIn('semestre_id', [3, 4]);
        }
        return $query;
    }

    /**
     * Scope pour les programmes avec enseignants
     */
    public function scopeWithEnseignants($query)
    {
        return $query->whereHas('enseignants');
    }

    /**
     * Scope pour les programmes sans enseignants
     */
    public function scopeWithoutEnseignants($query)
    {
        return $query->whereDoesntHave('enseignants');
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes Utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si c'est une UE
     */
    public function isUe(): bool
    {
        return $this->type === self::TYPE_UE;
    }

    /**
     * Vérifie si c'est un EC
     */
    public function isEc(): bool
    {
        return $this->type === self::TYPE_EC;
    }

    /**
     * Vérifie si l'UE a des ECs
     */
    public function hasElements(): bool
    {
        return $this->elements()->count() > 0;
    }

    /**
     * Retourne le nom complet (code + nom)
     */
    public function getFullName(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * Retourne l'année académique (4 ou 5)
     */
    public function getAnneeAttribute(): int
    {
        return in_array($this->semestre_id, [1, 2]) ? 4 : 5;
    }

    /**
     * Retourne le semestre de l'année (1 ou 2)
     */
    public function getSemestreAnneeAttribute(): int
    {
        return in_array($this->semestre_id, [1, 3]) ? 1 : 2;
    }

    /**
     * Retourne le nombre total d'heures pour un EC
     */
    public function getTotalHeures(): int
    {
        if ($this->isUe()) {
            return $this->elements->sum(function ($ec) {
                return $ec->getTotalHeures();
            });
        }

        return $this->enseignants->sum(function ($enseignant) {
            return $enseignant->pivot->heures_cm +
                   $enseignant->pivot->heures_td +
                   $enseignant->pivot->heures_tp;
        });
    }

    /**
     * Retourne le détail des heures par type
     */
    public function getHeuresDetail(): array
    {
        if ($this->isUe()) {
            $cm = $td = $tp = 0;
            foreach ($this->elements as $ec) {
                $detail = $ec->getHeuresDetail();
                $cm += $detail['cm'];
                $td += $detail['td'];
                $tp += $detail['tp'];
            }
            return compact('cm', 'td', 'tp');
        }

        return [
            'cm' => $this->enseignants->sum('pivot.heures_cm'),
            'td' => $this->enseignants->sum('pivot.heures_td'),
            'tp' => $this->enseignants->sum('pivot.heures_tp'),
        ];
    }

    /**
     * Retourne le total de crédits pour une UE
     */
    public function getTotalCredits(): int
    {
        if ($this->isUe()) {
            return $this->elements->sum('credits');
        }
        return $this->credits ?? 0;
    }

    /**
     * Assigner un enseignant à un EC
     */
    public function assignerEnseignant(
        User $user,
        int $heuresCm = 0,
        int $heuresTd = 0,
        int $heuresTp = 0,
        bool $isResponsable = false,
        ?string $note = null
    ): void {
        if (!$this->isEc()) {
            throw new \Exception('Seuls les ECs peuvent avoir des enseignants assignés.');
        }

        if (!$user->hasRole('teacher')) {
            throw new \Exception('L\'utilisateur doit avoir le rôle "teacher".');
        }

        // Si on définit comme responsable, retirer le statut des autres
        if ($isResponsable) {
            $this->enseignants()->updateExistingPivot(
                $this->enseignants->pluck('id'),
                ['is_responsable' => false]
            );
        }

        $this->enseignants()->syncWithoutDetaching([
            $user->id => [
                'heures_cm' => $heuresCm,
                'heures_td' => $heuresTd,
                'heures_tp' => $heuresTp,
                'is_responsable' => $isResponsable,
                'note' => $note,
            ]
        ]);
    }

    /**
     * Retirer un enseignant d'un EC
     */
    public function retirerEnseignant(User $user): void
    {
        $this->enseignants()->detach($user->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Method
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        // Définir l'ordre automatiquement lors de la création
        static::creating(function ($programme) {
            if (!$programme->order) {
                $maxOrder = static::where('parent_id', $programme->parent_id)
                    ->where('type', $programme->type)
                    ->where('semestre_id', $programme->semestre_id)
                    ->max('order');
                $programme->order = ($maxOrder ?? 0) + 1;
            }
        });

        // Valider que les ECs ont un parent_id et que les UEs n'en ont pas
        static::saving(function ($programme) {
            if ($programme->type === self::TYPE_EC && !$programme->parent_id) {
                throw new \Exception('Un EC doit avoir une UE parente.');
            }
            if ($programme->type === self::TYPE_UE && $programme->parent_id) {
                throw new \Exception('Une UE ne peut pas avoir de parent.');
            }
        });

        // Supprimer les relations avec les enseignants lors de la suppression
        static::deleting(function ($programme) {
            $programme->enseignants()->detach();
        });
    }
}