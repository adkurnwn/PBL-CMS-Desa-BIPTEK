<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Region\Province;
use App\Models\Region\Regency;
use App\Models\Region\District;
use App\Models\Region\Village;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'logo',
        'history',
        'vision',
        'mission',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'hero_images',
        'organization_image',
        'lembaga',
        'fasilitas',
        'primary_color',
        'secondary_color'
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'provinsi');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'kabupaten');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'kecamatan');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'kelurahan');
    }
}
