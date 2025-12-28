<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentView extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'viewed_at',
        'downloaded_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
