<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
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
        ];
    }

    // 🔹 Relation avec Profil
    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    // 🔹 Relations avec Niveau et Parcour
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function parcour()
    {
        return $this->belongsTo(Parcour::class);
    }

    // 🔹 Relations pour enseignants
    public function teacherNiveaux()
    {
        return $this->belongsToMany(Niveau::class, 'niveau_user');
    }

    public function teacherParcours()
    {
        return $this->belongsToMany(Parcour::class, 'parcour_user');
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
}
