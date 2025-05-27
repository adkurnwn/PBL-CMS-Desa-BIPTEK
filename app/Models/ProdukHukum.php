<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukHukum extends Model
{
    protected $table = 'produk_hukum';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'user_id',
        'link',
        'file',
        'date',
        'is_moderate',
        'views',
        'downloads',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
