<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ScheduleBackup extends Command
{
    protected $signature = 'app:create-database-backup';
    protected $description = 'Create a database backup using the spatie/laravel-backup package';

    public function handle()
    {
        $this->info('Starting scheduled database backup...');

        try {
            // Run backup command from spatie/laravel-backup
            Artisan::call('backup:run', ['--only-db' => true]);

            // Log success
            $this->info('Database backup completed successfully.');

            return self::SUCCESS; // Return success code
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            Log::error('Scheduled backup failed: ' . $e->getMessage());

            return self::FAILURE; // Return failure code
        }
    }
}
