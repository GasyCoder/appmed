<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // ✅ NOUVELLE RELATION
    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class);
    }

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
     * sinon visible si l'un des roles de l'utilisateur est présent dans audience_roles
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

    // ✅ NOUVEAU SCOPE - Annonces non vues par un user
    public function scopeUnviewedBy(Builder $q, $user): Builder
    {
        if (!$user) return $q;

        return $q->whereDoesntHave('views', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    // ✅ NOUVELLE MÉTHODE - Enregistrer une vue unique
    public function registerView(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$user) return;

        try {
            DB::beginTransaction();

            $exists = $this->views()->where('user_id', $user->id)->exists();
            if (!$exists) {
                $this->views()->create(['user_id' => $user->id]);
                Log::info("Announcement {$this->id}: Vue enregistrée pour user {$user->id}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Announcement {$this->id}: Erreur registerView - " . $e->getMessage());
        }
    }
}