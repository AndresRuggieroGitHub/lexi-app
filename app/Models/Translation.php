<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    public $timestamps = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'source_word_id',
        'target_word_id',
        'context_note',
        'created_at',
    ];

    public function sourceWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'source_word_id');
    }

    public function targetWord(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'target_word_id');
    }
}