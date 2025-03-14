<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'news_id',
        'gallery_id',
        'alt',
        'path',
        'is_visible',
    ];
}
