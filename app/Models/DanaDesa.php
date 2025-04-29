<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanaDesa extends Model
{
    protected $table = 'dana_desa';
    protected $fillable = [
        'title',
        'user_id',
        'file',
        'link',
        'date'
    ];
}
