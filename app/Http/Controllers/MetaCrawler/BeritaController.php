<?php

namespace App\Http\Controllers\MetaCrawler;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function show($slug)
    {
        // Fetch news data
        $news = News::with(['user', 'images', 'category'])
            ->where('slug', $slug)
            ->first();

        if (!$news) {
            return Inertia::render('FrontEnd/BeritaDetail', [
                'slug' => $slug,
                'meta' => [
                    'title' => 'Berita Tidak Ditemukan',
                    'description' => 'Halaman berita yang Anda cari tidak ditemukan.',
                ]
            ]);
        }

        // Prepare meta data
        $profile = Profile::with(['district', 'regency', 'province'])->first();
        $siteName = $profile ? $profile->name : 'Desa';

        $keywords = $siteName;
        if ($profile) {
            if ($profile->district) $keywords .= ', Kecamatan ' . ucwords(strtolower($profile->district->name));
            if ($profile->regency) $keywords .= ', ' . ucwords(strtolower($profile->regency->name));
            if ($profile->province) $keywords .= ', Provinsi ' . ucwords(strtolower($profile->province->name));
        }
        
        // Clean content for description
        $cleanContent = strip_tags($news->content);
        $description = strlen($cleanContent) > 160 
            ? substr($cleanContent, 0, 157) . '...' 
            : $cleanContent;

        // Prepare image URL
        $imageUrl = null;
        if ($news->images && $news->images->count() > 0) {
            $imageUrl = url('/storage/' . $news->images->first()->path);
        } else {
            $imageUrl = url('/storage/favicon.png');
        }

        $metaData = [
            'robots' => 'index, follow',
            'googlebot' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
            'google-bot-news' => 'index, follow',
            'title' => $news->title . ' - ' . $siteName,
            'keywords' => $keywords,
            'description' => $description ?: 'Baca berita terbaru dari desa kami',
            'og:type' => 'article',
            'og:title' => $news->title . ' - ' . $siteName,
            'og:description' => $description ?: 'Baca berita terbaru dari desa kami',
            'og:url' => url('/berita/' . $slug),
            'og:site_name' => $siteName,
            'og:image' => $imageUrl,
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $news->title . ' - ' . $siteName,
            'twitter:description' => $description ?: 'Baca berita terbaru dari desa kami',
            'twitter:image' => $imageUrl,
            'article:published_time' => $news->created_at->toISOString(),
            'article:author' => $news->user ? $news->user->name : 'Admin',
            'article:section' => $news->category ? $news->category->name : null,
        ];

        // Remove null values
        $metaData = array_filter($metaData, function($value) {
            return $value !== null;
        });

        return Inertia::render('FrontEnd/BeritaDetail', [
            'slug' => $slug,
            'meta' => $metaData,
            'canonicalUrl' => url('/berita/' . $slug),
        ]);
    }
}
