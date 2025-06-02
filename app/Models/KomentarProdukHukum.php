<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomentarProdukHukum extends Model
{
    protected $table = 'komentar_produk_hukum';
    protected $hidden = ['updated_at'];

    protected $fillable = [
        'id_produk_hukum',
        'id_ref_komentar',
        'nama_user',
        'is_approved',
        'komentar',
        'email_user',
    ];

    // Relationship with ProdukHukum
    public function produk_hukum()
    {
        return $this->belongsTo(ProdukHukum::class, 'id_produk_hukum');
    }

    // Self-referencing relationship for nested comments
    public function replies()
    {
        return $this->hasMany(KomentarProdukHukum::class, 'id_ref_komentar');
    }

    public function parent()
    {
        return $this->belongsTo(KomentarProdukHukum::class, 'id_ref_komentar');
    }
}
