<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'address',
        'hero_images',
        'organization_image',
        'lembaga',
        'fasilitas',
        'primary_color',
        'secondary_color'
    ];
}
