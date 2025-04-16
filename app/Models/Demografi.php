<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demografi extends Model
{
    protected $table = 'demografi';
    protected $fillable = [
        'user_id',
        'penduduk_pria',
        'penduduk_wanita',
        'tahun',
        'jumlah_peserta_bpjs',
        'jumlah_pindah_keluar',
        'jumlah_pendatang',
        'tamat_pt',
        'tamat_sma',
        'tamat_smp',
        'tamat_sd',
        'jumlah_lansia',
        'jumlah_dewasa',
        'jumlah_remaja',
        'jumlah_anak',
        'jumlah_penduduk',
        'jumlah_keluarga'
    ];
}
