<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
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

    public function eventEmails()
    {
        return $this->hasMany(EventEmails::class);
    }
}
