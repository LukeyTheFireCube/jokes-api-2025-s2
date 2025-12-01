<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Category_Joke extends Pivot
{
    protected $table = 'category_joke';

    protected $fillable = [
        'category_id',
        'joke_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function joke()
    {
        return $this->belongsTo(Joke::class);
    }
}

