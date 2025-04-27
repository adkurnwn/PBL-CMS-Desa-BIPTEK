<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEmails extends Model
{
    protected $fillable = [
        'event_id',
        'email',
        'is_sent'
    ];

    // Add composite unique rule
    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $exists = static::where('event_id', $model->event_id)
                          ->where('email', $model->email)
                          ->exists();
            if ($exists) {
                return false;
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}