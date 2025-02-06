<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    // Table name (optional if the table name matches the plural form of the model name)
    protected $table = 'niveaux';

    // Mass assignable attributes
    protected $fillable = [
        'sigle',
        'name',
        'status',
    ];

    // Casting attributes
    protected $casts = [
        'status' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_niveaux', 'niveau_id', 'user_id');
    }

    public function semestres()
    {
        return $this->hasMany(Semestre::class);
    }

}
