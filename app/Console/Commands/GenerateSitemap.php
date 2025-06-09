<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap';

    public function handle(SitemapService $sitemapService)
    {
        $this->info('Generating sitemap...');
        
        try {
            $sitemapService->generate();
            $this->info('Sitemap generated successfully!');
            
            $sitemapPath = public_path('sitemap.xml');
            if (file_exists($sitemapPath)) {
                $this->info("Sitemap location: {$sitemapPath}");
                $this->info("File size: " . filesize($sitemapPath) . " bytes");
                $this->info("You can view it at: " . url('sitemap.xml'));
            }
        } catch (\Exception $e) {
            $this->error('Failed to generate sitemap: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
