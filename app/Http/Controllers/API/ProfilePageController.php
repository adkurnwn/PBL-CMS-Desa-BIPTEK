<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Demografi;

class ProfilePageController extends Controller
{
    public function index()
    {
        $profile = Profile::first();
        $kepalaDesa = User::whereHas('roles', function ($query) {
            $query->where('name', 'Kepala Desa');
        })->where('status', true)->first();
        $dataDemografi = Demografi::latest()->first();

        return response()->json([
            'name' => $profile->name,
            'hero_image' => json_decode($profile->hero_images)[1],
            'history' => $profile->history,
            'vision' => $profile->vision,
            'mission' => $profile->mission,
            'fasilitas' => $profile->fasilitas,
            'lembaga' => $profile->lembaga,
            'organization_image' => $profile->organization_image,
            'kepala_desa' => [
                'name' => $kepalaDesa->name,
                'photo_profile' => $kepalaDesa->photo_profile,
            ],
            'data_demografi' => $dataDemografi ? [
                'penduduk' => $dataDemografi->jumlah_penduduk,
                'kepala_keluarga' => $dataDemografi->jumlah_keluarga,
                'penduduk_laki_laki' => $dataDemografi->penduduk_pria,
                'penduduk_perempuan' => $dataDemografi->penduduk_wanita,
            ] : [
                'penduduk' => 0,
                'kepala_keluarga' => 0,
                'penduduk_laki_laki' => 0,
                'penduduk_perempuan' => 0,
            ]
        ]);
    }
}
