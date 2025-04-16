<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'description',
        'date',
        'time',
        'location',
    ];

    private function user()
    {
        return $this->belongsTo(User::class);
    }
}
