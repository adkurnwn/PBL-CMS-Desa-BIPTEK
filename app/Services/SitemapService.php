<?php

namespace App\Services;

use App\Models\DanaDesa;
use App\Models\News;
use App\Models\Event;
use App\Models\ProdukDesa;
use App\Models\ProdukHukum;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    public function generate(): void
    {
        try {
            Log::info('Starting sitemap generation');
            $sitemap = Sitemap::create();

            // Add static pages
            $this->addStaticPages($sitemap);

            // Add news pages
            $this->addNewsPages($sitemap);

            // Add produk hukum pages
            $this->addProdukHukumPages($sitemap);

            // Add produk desa pages
            $this->addProdukDesaPages($sitemap);

            // Add dana desa pages
            $this->addDanaDesaPages($sitemap);

            // Write to public directory
            $sitemapPath = public_path('sitemap.xml');
            $sitemap->writeToFile($sitemapPath);

            // Verify file was created
            if (file_exists($sitemapPath)) {
                $fileSize = filesize($sitemapPath);
                Log::info("Sitemap generated successfully at {$sitemapPath} (Size: {$fileSize} bytes)");
            } else {
                Log::error('Sitemap file was not created');
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate sitemap: ' . $e->getMessage());
            throw $e;
        }
    }

    private function addStaticPages(Sitemap $sitemap): void
    {
        Log::info('Adding static pages to sitemap');
        
        // Add main pages
        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        $sitemap->add(
            Url::create(route('berita'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );

        $sitemap->add(
            Url::create(route('event'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
        );

        $sitemap->add(
            Url::create(route('informasi'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7)
        );

        $sitemap->add(
            Url::create(route('profil-desa'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.6)
        );

        $sitemap->add(
            Url::create(route('layanan'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6)
        );

        $sitemap->add(
            Url::create(route('produk-hukum'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6)
        );

        $sitemap->add(
            Url::create(route('dana-desa'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.6)
        );
    }

    private function addNewsPages(Sitemap $sitemap): void
    {
        $newsCount = 0;
        News::where('is_draft', false)
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($newsItems) use ($sitemap, &$newsCount) {
                foreach ($newsItems as $news) {
                    try {
                        $sitemap->add(
                            Url::create(route('berita.show', $news->slug))
                                ->setLastModificationDate($news->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                        $newsCount++;
                    } catch (\Exception $e) {
                        Log::warning("Failed to add news item {$news->slug} to sitemap: " . $e->getMessage());
                    }
                }
            });
        Log::info("Added {$newsCount} news items to sitemap");
    }

    private function addProdukHukumPages(Sitemap $sitemap): void
    {
        $hukumCount = 0;
        ProdukHukum::orderBy('updated_at', 'desc')
            ->chunk(100, function ($hukumItems) use ($sitemap, &$hukumCount) {
                foreach ($hukumItems as $hukum) {
                    try {
                        $sitemap->add(
                            Url::create(route('produk-hukum.show', $hukum->slug))
                                ->setLastModificationDate($hukum->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                        $hukumCount++;
                    } catch (\Exception $e) {
                        Log::warning("Failed to add produk hukum item {$hukum->slug} to sitemap: " . $e->getMessage());
                    }
                }
            });
        Log::info("Added {$hukumCount} produk hukum items to sitemap");
    }

    private function addProdukDesaPages(Sitemap $sitemap): void
    {
        $DesaCount = 0;
        ProdukDesa::orderBy('updated_at', 'desc')
            ->chunk(100, function ($desaItems) use ($sitemap, &$desaCount) {
                foreach ($desaItems as $desa) {
                    try {
                        $sitemap->add(
                            Url::create(route('produk-desa.detail', $desa->slug))
                                ->setLastModificationDate($desa->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                        $desaCount++;
                    } catch (\Exception $e) {
                        Log::warning("Failed to add produk desa {$desa->slug} to sitemap: " . $e->getMessage());
                    }
                }
            });
        Log::info("Added {$desaCount} produk desa items to sitemap");
    }

    private function addDanaDesaPages(Sitemap $sitemap): void
    {
        $DanaCount = 0;
        DanaDesa::orderBy('updated_at', 'desc')
            ->chunk(100, function ($danaItems) use ($sitemap, &$danaCount) {
                foreach ($danaItems as $dana) {
                    try {
                        $sitemap->add(
                            Url::create(route('dana-desa.show', $dana->slug))
                                ->setLastModificationDate($dana->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                        $danaCount++;
                    } catch (\Exception $e) {
                        Log::warning("Failed to add dana desa {$dana->slug} to sitemap: " . $e->getMessage());
                    }
                }
            });
        Log::info("Added {$danaCount} dana desa items to sitemap");
    }
}
