<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcour extends Model
{
    protected $table = 'parcours';

    protected $fillable = [
        'sigle',
        'name',
        'status',
    ];

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
        return $this->belongsToMany(User::class, 'parcour_user');
    }
}