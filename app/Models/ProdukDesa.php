<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukDesa extends Model
{
    protected $table = 'produk_desa';
    protected $fillable = [
        'user_id',
        'product_name',
        'slug',
        'price',
        'instagram',
        'facebook',
        'x',
        'tiktok',
        'youtube',
        'status',
        'alamat_penjual',
        'nama_penjual',
        'kontak_penjual',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(GambarProdukDesa::class);
    }
}
