<?php

namespace App\Http\Controllers\MetaCrawler;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function show($slug)
    {
        // Fetch gallery data
        $gallery = Gallery::with(['user', 'images', 'category'])
            ->where('slug', $slug)
            ->first();

        if (!$gallery) {
            return Inertia::render('FrontEnd/GaleriDetail', [
                'slug' => $slug,
                'meta' => [
                    'title' => 'Galeri Tidak Ditemukan',
                    'description' => 'Galeri yang Anda cari tidak ditemukan.',
                ]
            ]);
        }

        // Prepare meta data
        $profile = Profile::with(['district', 'regency', 'province'])->first();
        $siteName = $profile ? $profile->name : 'Desa';

        $keywords = $siteName;
        
        $description = strlen($gallery->description) > 160 
            ? substr($gallery->description, 0, 157) . '...' 
            : $gallery->description;

        // Prepare image URL
        $imageUrl = null;
        if ($gallery->images && $gallery->images->count() > 0) {
            $imageUrl = url('/storage/' . $gallery->images->first()->path);
        } else {
            $imageUrl = url('/storage/favicon.png');
        }

        $metaData = [
            'robots' => 'index, follow',
            'googlebot' => 'index, follow, max-snippet:-1, max-image-preview:large',
            'title' => $gallery->title . ' - ' . $siteName,
            'keywords' => $keywords,
            'description' => $description ?: 'Lihat Galeri terbaru dari desa kami',
            'og:type' => 'article',
            'og:title' => $gallery->title . ' - ' . $siteName,
            'og:description' => $description ?: 'Baca berita terbaru dari desa kami',
            'og:url' => url('/berita/' . $slug),
            'og:site_name' => $siteName,
            'og:image' => $imageUrl,
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $gallery->title . ' - ' . $siteName,
            'twitter:description' => $description ?: 'Baca berita terbaru dari desa kami',
            'twitter:image' => $imageUrl,
            'article:published_time' => $gallery->created_at->toISOString(),
            'article:author' => $gallery->user ? $gallery->user->name : 'Admin',
            'article:section' => $gallery->category ? $gallery->category->name : null,
        ];

        // Remove null values
        $metaData = array_filter($metaData, function($value) {
            return $value !== null;
        });

        return Inertia::render('FrontEnd/GaleriDetail', [
            'slug' => $slug,
            'meta' => $metaData,
            'canonicalUrl' => url('/galeri/' . $slug),
        ]);
    }
}
