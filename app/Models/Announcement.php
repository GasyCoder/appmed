<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'action_label',
        'action_url',
        'is_active',
        'audience_roles',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'audience_roles' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(function ($qq) {
                $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($qq) {
                $qq->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * audience_roles NULL => visible à tous
     * sinon visible si l’un des roles de l’utilisateur est présent dans audience_roles
     */
    public function scopeForUser(Builder $q, $user): Builder
    {
        if (!$user) return $q->whereRaw('1=0');

        $roles = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->values()->all()
            : [];

        return $q->where(function ($qq) use ($roles) {
            $qq->whereNull('audience_roles');

            foreach ($roles as $r) {
                // JSON_CONTAINS pour MySQL
                $qq->orWhereRaw("JSON_CONTAINS(audience_roles, JSON_QUOTE(?))", [$r]);
            }
        });
    }
}
