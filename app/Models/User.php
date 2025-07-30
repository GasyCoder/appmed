<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Niveau “one-to-one” via users.niveau_id
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    /**
     * Parcours “one-to-one” via users.parcour_id
     */
    public function parcour()
    {
        return $this->belongsTo(Parcour::class, 'parcour_id');
    }

    /**
     * Profil “one-to-one”
     */
    public function profil()
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

    //
    // ─── SCOPES ────────────────────────────────────────────────────────────────────
    //

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
     * Statistiques de l'enseignant
     */
    public function getTeacherStatsAttribute(): array
    {
        return [
            'niveaux_count'  => $this->teacherNiveaux()->count(),
            'parcours_count' => $this->teacherParcours()->count(),
            'documents_count'=> $this->documents()->count(),
        ];
    }

    /**
     * Documents uploadés par l'utilisateur
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Vérifie l'accès à un document (admin / teacher / student)
     */
    public function canAccessDocument(string $path): bool
    {
        // Admin
        if ($this->roles->contains('name', 'admin')) {
            return true;
        }

        // Enseignant -> ses propres ou ceux de ses niveaux
        if ($this->roles->contains('name', 'teacher')) {
            return Document::where('file_path', $path)
                ->where(fn($q) => $q->where('uploaded_by', $this->id)
                                   ->orWhereIn('niveau_id', $this->teacherNiveaux()->pluck('niveaux.id')))
                ->exists();
        }

        // Étudiant -> niveau & parcours courants + actif
        if ($this->roles->contains('name', 'student')) {
            return Document::where('file_path', $path)
                ->where('niveau_id', $this->niveau_id)
                ->where('parcour_id', $this->parcour_id)
                ->where('is_actif', true)
                ->exists();
        }

        return false;
    }

    /**
     * Nom complet avec grade (profil)
     */
    public function getFullNameWithGradeAttribute(): string
    {
        $grade = optional($this->profil)->grade;
        return $grade ? "{$grade}. {$this->name}" : $this->name;
    }
}
