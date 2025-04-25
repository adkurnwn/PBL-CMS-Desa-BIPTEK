<?php

use App\Console\Commands\SendEventReminders;

return [
    'tasks' => [
        SendEventReminders::class => [
            'frequency' => 'everyMinute',
        ],
    ],
];