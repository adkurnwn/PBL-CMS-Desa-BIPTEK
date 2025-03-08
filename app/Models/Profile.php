<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'logo', 'history', 'vision', 'mission', 'kabupaten', 'kecamatan', 'kelurahan'];
}
