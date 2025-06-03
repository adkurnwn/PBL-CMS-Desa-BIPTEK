<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        'kode_pos',
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

    /**
     * Get the enum values for primary_color column
     */
    public static function getPrimaryColorOptions()
    {
        $type = DB::select("SHOW COLUMNS FROM profiles WHERE Field = 'primary_color'")[0]->Type;
        preg_match('/^enum\((.+)\)$/', $type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        $enumValues = [];
        foreach (explode(',', $matches[1]) as $value) {
            $value = trim($value, "'\"");
            switch ($value) {
                case '#244031':
                    $enumValues[$value] = 'Green Forest';
                    break;
                case '#242540':
                    $enumValues[$value] = 'Denim';
                    break;
                case '#882828':
                    $enumValues[$value] = 'Maroon';
                    break;
                default:
                    $enumValues[$value] = 'Color ' . $value;
            }
        }

        return $enumValues;
    }

    /**
     * Get the enum values for secondary_color column
     */
    public static function getSecondaryColorOptions()
    {
        $type = DB::select("SHOW COLUMNS FROM profiles WHERE Field = 'secondary_color'")[0]->Type;
        preg_match('/^enum\((.+)\)$/', $type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        $enumValues = [];
        foreach (explode(',', $matches[1]) as $value) {
            $value = trim($value, "'\"");
            switch ($value) {
                case '#3B614A':
                    $enumValues[$value] = 'Aksen Green Forest';
                    break;
                case '#3B4659':
                    $enumValues[$value] = 'Aksen Denim';
                    break;
                case '#9F4941':
                    $enumValues[$value] = 'Aksen Maroon';
                    break;
                default:
                    $enumValues[$value] = 'Color ' . $value;
            }
        }

        return $enumValues;
    }
}
