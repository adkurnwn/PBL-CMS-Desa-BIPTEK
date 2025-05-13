<?php

use App\Console\Commands\SendEventReminders;
use App\Console\Commands\ScheduleBackup;
use Illuminate\Console\Scheduling\Schedule;

return [
    'tasks' => [
        SendEventReminders::class => [
            'frequency' => 'everyMinute',
        ],
        ScheduleBackup::class => [
            'frequency' => 'everyMinute',
        ],
    ],
];
