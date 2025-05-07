<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'content',
        'category_id',
        'video',
        'views',
        'is_draft',
        'is_moderate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(KomentarBerita::class, 'id_berita');
    }
}
