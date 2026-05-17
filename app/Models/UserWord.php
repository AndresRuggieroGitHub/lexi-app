<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWord extends Model
{
    protected $fillable = [
        'user_id',
        'word_id',
        'status',
        'is_favorite',
        'success_count',
        'fail_count',
        'last_seen_at',
        'next_review_at',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'last_seen_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}