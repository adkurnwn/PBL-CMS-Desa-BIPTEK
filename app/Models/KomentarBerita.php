<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomentarBerita extends Model
{
    protected $table = 'komentar_berita';

    protected $fillable = [
        'id_berita',
        'id_ref_komentar',
        'nama_user',
        'email',
        'is_approved',
        'komentar'
    ];

    // Relationship with News
    public function news()
    {
        return $this->belongsTo(News::class, 'id_berita');
    }

    // Self-referencing relationship for nested comments
    public function replies()
    {
        return $this->hasMany(KomentarBerita::class, 'id_ref_komentar');
    }

    public function parent()
    {
        return $this->belongsTo(KomentarBerita::class, 'id_ref_komentar');
    }
}
