<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CacheInvalidationRequested;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearMultipleCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CacheInvalidationRequested $event)
    {
        foreach ($event->keys as $key) {
            Cache::forget($key);
        }
    }
}
