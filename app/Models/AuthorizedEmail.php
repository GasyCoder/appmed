<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class AuthorizedEmail extends Model
{
    protected $fillable = [
        'email',
        'is_registered',
        'verification_token',
        'token_expires_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // ✅ Si déjà enregistré, pas besoin de token
            if ((bool) $model->is_registered) {
                $model->verification_token = null;
                $model->token_expires_at = null;
                return;
            }

            // ✅ Sinon, générer token uniquement si manquant
            if (empty($model->verification_token)) {
                $model->verification_token = (string) Str::uuid();
            }

            if (empty($model->token_expires_at)) {
                $model->token_expires_at = now()->addHours(2);
            }
        });
    }
}
