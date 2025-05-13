<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $tasks = config('scheduler.tasks', []);

            foreach ($tasks as $task => $config) {
                $schedule->command($task)->{$config['frequency']}();
            }
        });
    }
}
