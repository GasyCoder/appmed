<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'code',
        'name',
        'order',
        'parent_id',
        'semestre_id',
        'niveau_id',
        'parcour_id',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relations
    public function elements()
    {
        return $this->hasMany(Programme::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Programme::class, 'parent_id');
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

    // Scopes
    public function scopeUes($query)
    {
        return $query->whereNull('parent_id')->where('type', 'UE');
    }

    public function scopeEcs($query)
    {
        return $query->whereNotNull('parent_id')->where('type', 'EC');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Custom Methods
    public function isUe()
    {
        return $this->type === 'UE';
    }

    public function isEc()
    {
        return $this->type === 'EC';
    }

    public function hasElements()
    {
        return $this->elements()->count() > 0;
    }

    public function getFullName()
    {
        return "{$this->code} - {$this->name}";
    }

    // Boot method pour gérer l'ordre automatiquement
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($programme) {
            if (!$programme->order) {
                $maxOrder = static::where('parent_id', $programme->parent_id)
                    ->where('type', $programme->type)
                    ->max('order');
                $programme->order = ($maxOrder ?? 0) + 1;
            }
        });
    }
}
