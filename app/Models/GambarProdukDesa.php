<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GambarProdukDesa extends Model
{
    protected $table = 'gambar_produk_desa';

    protected $fillable = [
        'produk_desa_id',
        'images',
    ];
}
