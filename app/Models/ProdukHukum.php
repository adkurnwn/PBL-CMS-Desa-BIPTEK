<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukHukum extends Model
{
    protected $table = 'produk_hukum';
    protected $fillable = [
        'title',
        'description',
        'produk_hukum_category_id',
        'user_id',
        'link',
        'file',
        'date',
        'is_moderate',
    ];

    public function category()
    {
        return $this->belongsTo(ProdukHukumCategory::class, 'produk_hukum_category_id');
    }
}
