<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'category',
        'video',
        'views',
        'is_moderate',
    ];
}
