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

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
