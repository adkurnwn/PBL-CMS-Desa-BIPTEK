<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
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