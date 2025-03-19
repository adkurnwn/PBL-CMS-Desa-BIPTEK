<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
