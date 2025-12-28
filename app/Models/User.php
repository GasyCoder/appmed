<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasApiTokens;
    use HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'niveau_id',
        'parcour_id',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'status'            => 'boolean',
    ];

    //
    // ─── RELATIONS ────────────────────────────────────────────────────────────────
    //

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function parcour()
    {
        return $this->belongsTo(Parcour::class, 'parcour_id');
    }

    /**
     * Profil "one-to-one"
     */
    public function profil(): HasOne
    {
        return $this->hasOne(Profil::class);
    }

    /**
     * Enseignants associés (pivot parcours ↔ users)
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'parcour_user');
    }

    /**
     * Tous les niveaux via pivot niveau_user
     */
    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'niveau_user')
                    ->withTimestamps();
    }

    /**
     * Tous les parcours via pivot parcour_user
     */
    public function parcours(): BelongsToMany
    {
        return $this->belongsToMany(Parcour::class, 'parcour_user')
                    ->withTimestamps();
    }

    /**
     * Niveaux enseignants actifs (avec filtre status)
     */
    public function teacherNiveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'niveau_user')
                    ->where('niveaux.status', true)
                    ->withTimestamps()
                    ->orderBy('niveaux.name');
    }

    /**
     * Parcours enseignants actifs (avec filtre status)
     */
    public function teacherParcours(): BelongsToMany
    {
        return $this->belongsToMany(Parcour::class, 'parcour_user')
                    ->where('parcours.status', true)
                    ->withTimestamps()
                    ->orderBy('parcours.name');
    }

    /**
     * Programmes (ECs) enseignés par cet enseignant
     */
    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'programme_user')
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'is_responsable', 'note'])
            ->withTimestamps()
            ->orderBy('programmes.semestre_id')
            ->orderBy('programmes.order');
    }

    /**
     * Programmes où l'enseignant est responsable
     */
    public function programmesResponsable(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'programme_user')
            ->wherePivot('is_responsable', true)
            ->withPivot(['heures_cm', 'heures_td', 'heures_tp', 'note'])
            ->withTimestamps()
            ->orderBy('programmes.semestre_id')
            ->orderBy('programmes.order');
    }

    /**
     * Documents uploadés par l'utilisateur
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    //
    // ─── SCOPES ────────────────────────────────────────────────────────────────────
    //

    /**
     * Récupère uniquement les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Récupère uniquement les enseignants actifs
     */
    public function scopeActiveTeachers($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                     ->where('status', true);
    }

    /**
     * Récupère uniquement les étudiants actifs
     */
    public function scopeActiveStudents($query)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', 'student'))
                     ->where('status', true);
    }

    /**
     * Récupère les utilisateurs par rôle
     */
    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    /**
     * Récupère les enseignants qui enseignent un programme spécifique
     */
    public function scopeTeachingProgramme($query, int $programmeId)
    {
        return $query->whereHas('programmes', fn($q) => $q->where('programmes.id', $programmeId));
    }

    //
    // ─── ACCESSORS & MUTATORS ─────────────────────────────────────────────────────
    //

    /**
     * Charge horaire totale de l'enseignant
     */
    public function getChargeHoraireAttribute(): array
    {
        $programmes = $this->programmes;
        
        return [
            'cm' => $programmes->sum('pivot.heures_cm'),
            'td' => $programmes->sum('pivot.heures_td'),
            'tp' => $programmes->sum('pivot.heures_tp'),
            'total' => $programmes->sum(function($p) {
                return $p->pivot->heures_cm + $p->pivot->heures_td + $p->pivot->heures_tp;
            }),
        ];
    }

    /**
     * Nombre de programmes enseignés
     */
    public function getProgrammesCountAttribute(): int
    {
        return $this->programmes()->count();
    }

    /**
     * Nom complet avec grade (profil)
     */
    public function getFullNameWithGradeAttribute(): string
    {
        $grade = optional($this->profil)->grade;
        return $grade ? "{$grade}. {$this->name}" : $this->name;
    }

    /**
     * Statistiques de l'enseignant
     */
    public function getTeacherStatsAttribute(): array
    {
        return [
            'niveaux_count'    => $this->teacherNiveaux()->count(),
            'parcours_count'   => $this->teacherParcours()->count(),
            'programmes_count' => $this->programmes()->count(),
            'documents_count'  => $this->documents()->count(),
            'charge_horaire'   => $this->charge_horaire,
        ];
    }

    //
    // ─── BUSINESS LOGIC ───────────────────────────────────────────────────────────
    //

    /**
     * Récupère les parcours disponibles pour un niveau spécifique
     */
    public function getParcoursForNiveau(int $niveau_id)
    {
        return $this->teacherParcours()
                    ->whereExists(fn($q) => $q->select(DB::raw(1))
                                              ->from('niveau_user')
                                              ->where('niveau_user.user_id', $this->id)
                                              ->where('niveau_user.niveau_id', $niveau_id))
                    ->get();
    }

    /**
     * Vérifie si l'utilisateur a accès à un niveau spécifique
     */
    public function hasAccessToNiveau(int $niveau_id): bool
    {
        return $this->teacherNiveaux()
                    ->where('niveaux.id', $niveau_id)
                    ->exists();
    }

    /**
     * Vérifie si l'utilisateur a accès à un parcours spécifique
     */
    public function hasAccessToParcours(int $parcour_id): bool
    {
        return $this->teacherParcours()
                    ->where('parcours.id', $parcour_id)
                    ->exists();
    }

    /**
     * Vérifie si l'enseignant a accès à un programme
     */
    public function hasAccessToProgramme(int $programmeId): bool
    {
        return $this->programmes()
            ->where('programmes.id', $programmeId)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur est responsable d'un programme
     */
    public function isResponsableOf(int $programmeId): bool
    {
        return $this->programmesResponsable()
            ->where('programmes.id', $programmeId)
            ->exists();
    }

    /**
     * Vérifie l'accès à un document (admin / teacher / student)
     */
    public function canAccessDocument(string $path): bool
    {
        // Admin
        if ($this->hasRole('admin')) {
            return true;
        }

        // Enseignant -> ses propres ou ceux de ses niveaux
        if ($this->hasRole('teacher')) {
            return Document::where('file_path', $path)
                ->where(fn($q) => $q->where('uploaded_by', $this->id)
                                   ->orWhereIn('niveau_id', $this->teacherNiveaux()->pluck('niveaux.id')))
                ->exists();
        }

        // Étudiant -> niveau & parcours courants + actif
        if ($this->hasRole('student')) {
            return Document::where('file_path', $path)
                ->where('niveau_id', $this->niveau_id)
                ->where('parcour_id', $this->parcour_id)
                ->where('is_actif', true)
                ->exists();
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est un enseignant
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Vérifie si l'utilisateur est un étudiant
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Assigne l'enseignant à un programme (EC)
     */
    public function assignToProgramme(
        Programme $programme,
        int $heuresCm = 0,
        int $heuresTd = 0,
        int $heuresTp = 0,
        bool $isResponsable = false,
        ?string $note = null
    ): void {
        if (!$this->isTeacher()) {
            throw new \Exception('Seuls les enseignants peuvent être assignés à des programmes.');
        }

        if (!$programme->isEc()) {
            throw new \Exception('Seuls les ECs peuvent avoir des enseignants assignés.');
        }

        // Si on définit comme responsable, retirer le statut des autres
        if ($isResponsable) {
            $programme->enseignants()->updateExistingPivot(
                $programme->enseignants->pluck('id'),
                ['is_responsable' => false]
            );
        }

        $this->programmes()->syncWithoutDetaching([
            $programme->id => [
                'heures_cm' => $heuresCm,
                'heures_td' => $heuresTd,
                'heures_tp' => $heuresTp,
                'is_responsable' => $isResponsable,
                'note' => $note,
            ]
        ]);
    }

    /**
     * Retire l'enseignant d'un programme
     */
    public function removeFromProgramme(Programme $programme): void
    {
        $this->programmes()->detach($programme->id);
    }

    /**
     * Met à jour les heures d'enseignement pour un programme
     */
    public function updateHeuresProgramme(
        Programme $programme,
        ?int $heuresCm = null,
        ?int $heuresTd = null,
        ?int $heuresTp = null,
        ?bool $isResponsable = null,
        ?string $note = null
    ): void {
        $updateData = array_filter([
            'heures_cm'      => $heuresCm,
            'heures_td'      => $heuresTd,
            'heures_tp'      => $heuresTp,
            'is_responsable' => $isResponsable,
            'note'           => $note,
        ], static fn ($value) => $value !== null);

        if ($updateData !== []) {
            $this->programmes()->updateExistingPivot($programme->id, $updateData);
        }
    }


    /**
     * Récupère les programmes par semestre pour cet enseignant
     */
    public function programmesBySemestre(int $semestreId)
    {
        return $this->programmes()
            ->where('programmes.semestre_id', $semestreId)
            ->get();
    }

    /**
     * Récupère les programmes par année pour cet enseignant
     */
    public function programmesByAnnee(int $annee)
    {
        $semestres = $annee == 4 ? [1, 2] : [3, 4];
        
        return $this->programmes()
            ->whereIn('programmes.semestre_id', $semestres)
            ->get();
    }
}