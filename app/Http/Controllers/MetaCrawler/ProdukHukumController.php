<?php

namespace App\Http\Controllers\MetaCrawler;

use App\Http\Controllers\Controller;
use App\Models\ProdukHukum;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProdukHukumController extends Controller
{
    public function show($slug)
    {
        // Fetch produkhukum data
        $produkhukum = ProdukHukum::with(['user', 'category'])
            ->where('slug', $slug)
            ->first();

        if (!$produkhukum) {
            return Inertia::render('FrontEnd/ProdukHukumDetail', [
                'slug' => $slug,
                'meta' => [
                    'title' => 'Produk Hukum Tidak Ditemukan',
                    'description' => 'Halaman Produk Hukum yang Anda cari tidak ditemukan.',
                ]
            ]);
        }


        // Prepare meta data
        $profile = Profile::with(['district', 'regency', 'province'])
            ->select(['name','hero_images', 'provinsi', 'kabupaten', 'kecamatan'])
            ->first();

        $siteName = $profile ? $profile->name : 'Desa';

        $keywords = $siteName;
        if ($profile) {
            if ($profile->district) $keywords .= ', Kecamatan ' . ucwords(strtolower($profile->district->name));
            if ($profile->regency) $keywords .= ', ' . ucwords(strtolower($profile->regency->name));
            if ($profile->province) $keywords .= ', Provinsi ' . ucwords(strtolower($profile->province->name));
        }
        
        // Clean content for description
        $cleanContent = strip_tags($produkhukum->content);
        $description = strlen($cleanContent) > 160 
            ? substr($cleanContent, 0, 157) . '...' 
            : $cleanContent;

        $metaData = [
            'robots' => 'index, follow',
            'googlebot' => 'index, follow, ',
            'title' => $produkhukum->title . ' - ' . $siteName,
            'keywords' => 'produk hukum' . $keywords,
            'description' => $description ?: 'Baca produk hukum terbaru dari desa kami',
            'og:type' => 'article',
            'og:title' => $produkhukum->title . ' - ' . $siteName,
            'og:description' => $description ?: 'Baca produk hukum terbaru dari desa kami',
            'og:url' => url('/produk-hukum/' . $slug),
            'og:site_name' => $siteName,
            'og:image' => $profile && $profile->hero_images ? url('/storage/' . (json_decode($profile->hero_images, true)[0] ?? 'favicon.png')) : url('/storage/favicon.png'),
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $produkhukum->title . ' - ' . $siteName,
            'twitter:description' => $description ?: 'Baca produk hukum terbaru dari desa kami',
            'twitter:image' => $profile && $profile->hero_images ? url('/storage/' . (json_decode($profile->hero_images, true)[0] ?? 'favicon.png')) : url('/storage/favicon.png'),
            'article:published_time' => $produkhukum->created_at->toISOString(),
            'article:author' => $produkhukum->user ? $produkhukum->user->name : 'Admin',
            'article:section' => $produkhukum->category ? $produkhukum->category->name : null,
        ];

        // Remove null values
        $metaData = array_filter($metaData, function($value) {
            return $value !== null;
        });

        return Inertia::render('FrontEnd/ProdukHukumDetail', [
            'slug' => $slug,
            'meta' => $metaData,
            'canonicalUrl' => url('/produk-hukum/' . $slug),
        ]);
    }
}
