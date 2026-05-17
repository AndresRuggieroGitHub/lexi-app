<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserCollection extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'user_id',
        'language_code',
        'name',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'collection_words', 'collection_id', 'word_id')->withTimestamps();
    }
}