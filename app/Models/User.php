<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function parcour()
    {
        return $this->belongsTo(Parcour::class);
    }

    // 🔹 Relation avec Profil
    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    // 🔹 Relations avec Niveau et Parcour
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'parcour_user');
    }

    public function niveaux()
    {
        return $this->belongsToMany(Niveau::class, 'niveau_user')
                    ->withTimestamps();
    }

    public function parcours()
    {
        return $this->belongsToMany(Parcour::class, 'parcour_user')
                    ->withTimestamps();
    }

    public function teacherNiveaux()
    {
        return $this->belongsToMany(Niveau::class, 'niveau_user')
                    ->where('status', true)
                    ->withTimestamps()
                    ->with(['semestres' => function($query) {
                        $query->where('status', true)
                            ->orderBy('name');
                    }]);
    }

    public function teacherParcours()
    {
        return $this->belongsToMany(Parcour::class, 'parcour_user')
                    ->where('status', true)
                    ->withTimestamps()
                    ->orderBy('name');
    }

    public function getTeacherStatsAttribute()
    {
        return [
            'niveaux_count' => $this->teacherNiveaux()->count() ?? 0,
            'parcours_count' => $this->teacherParcours()->count() ?? 0,
            'documents_count' => $this->documents()->count() ?? 0
        ];
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function canAccessDocument($path): bool
    {
        // Admin a accès à tout
        if ($this->roles->contains('name', 'admin')) {
            return true;
        }

        // Enseignant a accès à ses propres documents
        if ($this->roles->contains('name', 'teacher')) {
            return Document::where('file_path', $path)
                ->where(function($query) {
                    $query->where('uploaded_by', $this->id)
                        ->orWhereIn('niveau_id', $this->teacherNiveaux->pluck('id'));
                })
                ->exists();
        }

        // Étudiant a accès aux documents actifs de son niveau et parcours
        if ($this->roles->contains('name', 'student')) {
            return Document::where('file_path', $path)
                ->where('niveau_id', $this->niveau_id)
                ->where('parcour_id', $this->parcour_id)
                ->where('is_actif', true)
                ->exists();
        }

        return false;
    }

    public function getFullNameWithGradeAttribute()
    {
        $grade = optional($this->profil)->grade;
        return $grade ? "{$grade}. {$this->name}" : $this->name;
    }

}
