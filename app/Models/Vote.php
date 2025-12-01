<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsTo;

class Vote extends Model
{
    protected $fillable = [
        'user_id',
        'joke_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function joke(): BelongsTo
    {
        return $this->belongsTo(Joke::class);
    }
}

