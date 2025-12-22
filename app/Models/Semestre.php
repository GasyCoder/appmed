<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = [
        'name',
        'niveau_id',
        'is_active',
        'status'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status' => 'boolean'
    ];

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
