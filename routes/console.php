<?php

use App\Http\Controllers\Dashboard\RecoveryController;
use Illuminate\Support\Facades\Schedule;


$frequency = env('BACKUP_FREQUENCY', 'daily');
$time = env('BACKUP_TIME', '00:00');

$backupCommand = Schedule::call(function () {
    $recoveryController = new RecoveryController();
    $recoveryController->create();
});

switch ($frequency) {
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
