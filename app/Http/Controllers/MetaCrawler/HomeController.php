<?php

namespace App\Http\Controllers\MetaCrawler;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get profile data
        $profile = Profile::first();
        $siteName = $profile ? $profile->name : 'Desa';
        
        // Prepare meta data for home page
        $metaData = [
            'title' => 'Website Resmi Pemerintah ' . $siteName,
            'description' => 'Website resmi pemerintah '.$siteName.' dengan informasi terkini tentang layanan publik, berita, dan kegiatan desa.',
            'og:type' => 'website',
            'og:title' => 'Website Resmi Pemerintah ' . $siteName,
            'og:description' => 'Website resmi pemerintah '.$siteName.' dengan informasi terkini tentang layanan publik, berita, dan kegiatan desa.',
            'og:url' => url('/'),
            'og:site_name' => 'CMS ' . $siteName,
            'og:image' => $profile && $profile->hero_images ? url('/storage/' . (json_decode($profile->hero_images, true)[0] ?? 'favicon.png')) : url('/storage/favicon.png'),
            'twitter:card' => 'summary_large_image',
            'twitter:title' => 'Website Resmi Pemerintah ' . $siteName,
            'twitter:description' => $profile ? $profile->description : 'Website resmi pemerintah desa dengan informasi terkini tentang layanan publik, berita, dan kegiatan desa.',
            'twitter:image' => $profile && $profile->hero_images ? url('/storage/' . (json_decode($profile->hero_images, true)[0] ?? 'favicon.png')) : url('/storage/favicon.png'),
        ];

        // Remove null values
        $metaData = array_filter($metaData, function($value) {
            return $value !== null;
        });

        return Inertia::render('FrontEnd/HomeView', [
            'meta' => $metaData,
            'canonicalUrl' => url('/'),
        ]);
    }
}
