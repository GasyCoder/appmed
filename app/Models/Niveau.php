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

        // Relations
    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

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
        return $this->belongsToMany(User::class, 'niveau_user')
                    ->withTimestamps();
    }

    public function semestres()
    {
        return $this->hasMany(Semestre::class);
    }


}
