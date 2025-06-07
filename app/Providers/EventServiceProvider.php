<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SitemapUpdateRequested;
use App\Listeners\UpdateSitemap;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // ...existing code...
        SitemapUpdateRequested::class => [
            UpdateSitemap::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
        
        Log::info('EventServiceProvider booted');
        Log::info('Registered events: ' . json_encode($this->listen));
    }
}