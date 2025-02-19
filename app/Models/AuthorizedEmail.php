<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class AuthorizedEmail extends Model
{
    protected $fillable = ['email', 'is_registered', 'verification_token', 'token_expires_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->verification_token = Str::uuid();
            $model->token_expires_at = now()->addHours(2);
        });
    }

}
