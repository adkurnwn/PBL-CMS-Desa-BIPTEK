<?php

namespace App\Listeners;

use App\Events\SitemapUpdateRequested;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Log;

class UpdateSitemap
{
    protected $sitemapService;

    public function __construct(SitemapService $sitemapService)
    {
        $this->sitemapService = $sitemapService;
    }

    public function handle(SitemapUpdateRequested $event)
    {
        Log::info('=== SITEMAP UPDATE START ===');
        Log::info('SitemapUpdateRequested event received');
        Log::info('Event class: ' . get_class($event));
        Log::info('Listener class: ' . get_class($this));
        
        try {
            Log::info('Attempting to generate sitemap...');
            
            // Check if sitemap service is properly injected
            if (!$this->sitemapService) {
                Log::error('SitemapService is null - dependency injection failed');
                return;
            }
            
            Log::info('SitemapService injected successfully: ' . get_class($this->sitemapService));
            
            // Check if sitemap file exists before generation
            $sitemapPath = public_path('sitemap.xml');
            $existsBefore = file_exists($sitemapPath);
            $sizeBefore = $existsBefore ? filesize($sitemapPath) : 0;
            
            Log::info("Sitemap before generation - Exists: {$existsBefore}, Size: {$sizeBefore} bytes");
            
            // Generate sitemap
            $this->sitemapService->generate();
            
            // Check if sitemap file exists after generation
            $existsAfter = file_exists($sitemapPath);
            $sizeAfter = $existsAfter ? filesize($sitemapPath) : 0;
            $lastModified = $existsAfter ? date('Y-m-d H:i:s', filemtime($sitemapPath)) : 'N/A';
            
            Log::info("Sitemap after generation - Exists: {$existsAfter}, Size: {$sizeAfter} bytes, Modified: {$lastModified}");
            
            if ($existsAfter) {
                Log::info('Sitemap updated successfully after news change');
                Log::info("Sitemap location: {$sitemapPath}");
                Log::info("Sitemap URL: " . url('sitemap.xml'));
            } else {
                Log::error('Sitemap file was not created after generation');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to update sitemap after news change');
            Log::error('Exception: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        Log::info('=== SITEMAP UPDATE END ===');
    }
}
