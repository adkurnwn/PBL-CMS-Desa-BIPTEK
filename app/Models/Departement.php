<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Departement::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Departement::class, 'parent_id');
    }
}
