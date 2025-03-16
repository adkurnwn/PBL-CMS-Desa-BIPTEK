<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'content',
        'category',
        'video',
        'views',
        'is_moderate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
