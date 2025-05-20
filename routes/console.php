<?php

use Illuminate\Support\Facades\Schedule;


$frequency = env('BACKUP_FREQUENCY', 'daily');
$time = env('BACKUP_TIME', '00:00');

$backupCommand = Schedule::command('backup:run --only-db');

switch ($frequency) {
    case 'hourly':
        $backupCommand->hourly();
        break;
    case 'daily':
        $backupCommand->dailyAt($time);
        break;
    case 'weekly':
        $backupCommand->weeklyOn(1, $time);
        break;
    case 'monthly':
        $backupCommand->monthlyOn(1, $time);
        break;
    default:
        $backupCommand->dailyAt('00:00');
}
