<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Helpers\LogActivityHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Validation\UpdateThemeRequest;
use App\Http\Requests\Validation\UpdateSettingsRequest;

class SettingsController extends Controller
{
    protected $logHelper;
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->logHelper = new LogActivityHelper();
        $this->mediaService = $mediaService;
    }

    public function edit()
    {
        $profile = Profile::first();

        // Get current settings from .env file
        $settings = [
            'app_name' => env('APP_NAME'),
            'app_timezone' => env('APP_TIMEZONE'),
            'app_url' => env('APP_URL'),
            'db_connection' => env('DB_CONNECTION'),
            'db_host' => env('DB_HOST'),
            'db_port' => env('DB_PORT'),
            'db_database' => env('DB_DATABASE'),
            'db_username' => env('DB_USERNAME'),
            'mail_mailer' => env('MAIL_MAILER'),
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'favicon' => Storage::disk('public')->exists('favicon.png') ? 'favicon.png' : null,
            'primary_color' => $profile ? $profile->primary_color : '#244031',
            'secondary_color' => $profile ? $profile->secondary_color : '#3B614A',
            // Add backup settings
            'backup_enabled' => env('BACKUP_ENABLED', true),
            'backup_frequency' => env('BACKUP_FREQUENCY', 'daily'),
        ];

        // Get a list of available timezones
        $timezones = \DateTimeZone::listIdentifiers();

        // Get database connection types
        $dbConnections = ['mysql', 'pgsql', 'sqlite', 'sqlsrv'];

        // Get mail drivers
        $mailDrivers = ['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'log', 'array'];

        // Theme colors options - get from Profile model enum
        $colorOptions = Profile::getPrimaryColorOptions();
        $secondaryColorOptions = Profile::getSecondaryColorOptions();

        return Inertia::render('Dashboard/Settings/Edit', [
            'settings' => $settings,
            'timezones' => $timezones,
            'dbConnections' => $dbConnections,
            'mailDrivers' => $mailDrivers,
            'colorOptions' => $colorOptions,
            'secondaryColorOptions' => $secondaryColorOptions,
        ]);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $envFilePath = base_path('.env');
        $envContent = File::get($envFilePath);

        // Check if database settings are being changed
        $dbSettingsChanged = $this->isDatabaseSettingsChanged($request);

        if ($dbSettingsChanged) {
            try {
                // Step 1: Change session and cache drivers to file before DB changes
                $originalSessionDriver = env('SESSION_DRIVER');
                $originalCacheStore = env('CACHE_STORE');

                $envContent = $this->switchToFileDrivers($envContent);
                File::put($envFilePath, $envContent);

                // Clear config cache to apply file drivers immediately
                Artisan::call('config:clear');

                // Update runtime config immediately (like InstallController)
                Config::set('session.driver', 'file');
                Config::set('cache.default', 'file');

                // Step 2: Create backup of current database
                $this->createDatabaseBackup();

                // Step 3: Test new database connection
                $this->testDatabaseConnection($request);

                // Step 4: Update database settings in .env
                $envContent = File::get($envFilePath);
                $envContent = $this->updateDatabaseSettings($envContent, $request);
                File::put($envFilePath, $envContent);

                // Step 5: Update runtime database configuration (like InstallController)
                Config::set('database.connections.mysql.host', $request->db_host);
                Config::set('database.connections.mysql.port', $request->db_port);
                Config::set('database.connections.mysql.database', $request->db_database);
                Config::set('database.connections.mysql.username', $request->db_username);
                Config::set('database.connections.mysql.password', $request->db_password);

                // Refresh database connection (like InstallController)
                DB::purge('mysql');
                DB::reconnect('mysql');

                // Step 6: Run migrations on new database
                $this->runMigrationsOnNewDatabase();

                // Step 7: Switch session and cache drivers back to database
                $envContent = File::get($envFilePath);
                $envContent = $this->switchBackToDatabaseDrivers($envContent, $originalSessionDriver, $originalCacheStore);
                File::put($envFilePath, $envContent);

                // Final config updates (like InstallController)
                Config::set('session.driver', $originalSessionDriver ?: 'database');
                Config::set('cache.default', $originalCacheStore ?: 'database');
                Artisan::call('config:clear');
            } catch (Exception $e) {
                // Enhanced error handling with proper restoration
                Log::error("Database change failed: " . $e->getMessage());

                try {
                    $envContent = File::get($envFilePath);
                    $envContent = $this->switchBackToDatabaseDrivers($envContent, $originalSessionDriver ?? 'database', $originalCacheStore ?? 'database');
                    File::put($envFilePath, $envContent);

                    // Restore runtime config
                    Config::set('session.driver', $originalSessionDriver ?? 'database');
                    Config::set('cache.default', $originalCacheStore ?? 'database');
                    Artisan::call('config:clear');
                } catch (Exception $restoreException) {
                    Log::error('Failed to restore original drivers: ' . $restoreException->getMessage());
                }

                return redirect()->back()->with('error', 'Database change failed: ' . $e->getMessage());
            }
        }

        // Update other .env variables (non-database)
        $envContent = $this->updateEnvVariable($envContent, 'APP_NAME', $request->app_name);
        $envContent = $this->updateEnvVariable($envContent, 'APP_TIMEZONE', $request->app_timezone);
        $envContent = $this->updateEnvVariable($envContent, 'APP_URL', $request->app_url);

        // Mail settings
        if ($request->filled('mail_mailer')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_MAILER', $request->mail_mailer);
        }
        if ($request->filled('mail_host')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_HOST', $request->mail_host);
        }
        if ($request->filled('mail_port')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_PORT', $request->mail_port);
        }
        if ($request->filled('mail_username')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_USERNAME', $request->mail_username);
        }
        if ($request->filled('mail_password')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_PASSWORD', $request->mail_password);
        }
        if ($request->filled('mail_encryption')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_ENCRYPTION', $request->mail_encryption);
        }
        if ($request->filled('mail_from_address')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_FROM_ADDRESS', '"' . $request->mail_from_address . '"');
        }
        if ($request->filled('mail_from_name')) {
            $envContent = $this->updateEnvVariable($envContent, 'MAIL_FROM_NAME', '"' . $request->mail_from_name . '"');
        }

        // Add backup settings
        if ($request->has('backup_enabled')) {
            $envContent = $this->updateEnvVariable($envContent, 'BACKUP_ENABLED', $request->backup_enabled ? 'true' : 'false');
        }
        if ($request->filled('backup_frequency')) {
            $envContent = $this->updateEnvVariable($envContent, 'BACKUP_FREQUENCY', $request->backup_frequency);
        }

        // Write content back to .env file (if not already done for database changes)
        if (!$dbSettingsChanged) {
            File::put($envFilePath, $envContent);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $this->updateFavicon($request->file('favicon'));
        }

        // Update theme colors in the profile
        if ($request->filled('primary_color') || $request->filled('secondary_color')) {
            $profile = Profile::first();
            if ($profile) {
                $profileData = [];

                if ($request->filled('primary_color')) {
                    $profileData['primary_color'] = $request->primary_color;
                }

                if ($request->filled('secondary_color')) {
                    $profileData['secondary_color'] = $request->secondary_color;
                }

                $profile->update($profileData);
            }
        }

        // Log the activity
        $this->logHelper->logActivity('update', 'settings', 'Updated application settings');

        $message = $dbSettingsChanged ?
            'Pengaturan aplikasi web telah diperbarui dan database telah dimigrasikan' :
            'Pengaturan aplikasi web telah diperbarui';

        return redirect()->back()->with('success', $message);
    }

    public function updateTheme(UpdateThemeRequest $request)
    {
        // Update theme colors in the profile
        if ($request->filled('primary_color') || $request->filled('secondary_color')) {
            $profile = Profile::first();
            if (!$profile) {
                $profile = new Profile();
            }

            $profileData = [];

            if ($request->filled('primary_color')) {
                $profileData['primary_color'] = $request->primary_color;
            }

            if ($request->filled('secondary_color')) {
                $profileData['secondary_color'] = $request->secondary_color;
            }

            $result = $profile->updateOrCreate(['id' => $profile->id ?? 1], $profileData);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $this->updateFavicon($request->file('favicon'));
        }

        // Log the activity
        $this->logHelper->logActivity('update', 'theme_settings', 'Memperbarui pengaturan tema');

        return redirect()->back()->with('success', 'Pengaturan tema telah diperbarui');
    }

    /**
     * Update or create an environment variable
     */
    protected function updateEnvVariable($envContent, $key, $value)
    {
        // If value contains spaces, wrap it in quotes
        if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
            $value = '"' . $value . '"';
        }

        // Check if the key exists
        if (preg_match("/^{$key}=/m", $envContent)) {
            // Replace existing value
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        } else {
            // Add key-value pair
            return $envContent . "\n{$key}={$value}\n";
        }
    }

    /**
     * Update favicon from uploaded file
     */
    protected function updateFavicon($file)
    {
        // Process and save favicon
        $extension = $file->getClientOriginalExtension();
        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'ico'])) {
            // For PNG/JPG/JPEG/GIF, save directly as favicon.png
            Storage::disk('public')->put('favicon.png', file_get_contents($file));
        } else {
            // For other formats, handle differently or reject
            return false;
        }

        return true;
    }

    /**
     * Check if any database settings are being changed
     */
    protected function isDatabaseSettingsChanged($request)
    {
        $dbFields = ['db_connection', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password'];

        foreach ($dbFields as $field) {
            if ($request->filled($field) && $request->$field !== env(strtoupper($field))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create backup of current database
     */
    protected function createDatabaseBackup()
    {
        try {
            // Use RecoveryController's backup system instead
            $recoveryController = new \App\Http\Controllers\Dashboard\RecoveryController();
            $recoveryController->create();

            $this->logHelper->logActivity('create', 'database_backup', 'Database backup created before changing settings');
        } catch (Exception $e) {
            throw new Exception('Failed to create database backup: ' . $e->getMessage());
        }
    }

    public function testConnection(Request $request)
    {
        try {
            $this->testDatabaseConnection($request);
            return response()->json(['success' => true, 'message' => 'Database connection successful']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    protected function testDatabaseConnection($request)
    {
        try {
            // Create temporary database configuration
            $config = [
                'driver' => $request->db_connection ?: env('DB_CONNECTION'),
                'host' => $request->db_host ?: env('DB_HOST'),
                'port' => $request->db_port ?: env('DB_PORT'),
                'database' => $request->db_database ?: env('DB_DATABASE'),
                'username' => $request->db_username ?: env('DB_USERNAME'),
                'password' => $request->db_password ?: env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            // Set temporary database configuration
            Config::set('database.connections.test_connection', $config);

            // Test the connection
            $pdo = DB::connection('test_connection')->getPdo();

            if (!$pdo) {
                throw new Exception('Could not establish database connection');
            }

            // Test if we can run a simple query
            DB::connection('test_connection')->select('SELECT 1');

            // Log successful connection test
            $this->logHelper->logActivity('test', 'database_connection', 'Database connection test successful');
        } catch (Exception $e) {
            throw new Exception('Database connection test failed: ' . $e->getMessage());
        }
    }

    protected function updateDatabaseSettings($envContent, $request)
    {
        if ($request->filled('db_connection')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_CONNECTION', $request->db_connection);
        }
        if ($request->filled('db_host')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_HOST', $request->db_host);
        }
        if ($request->filled('db_port')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_PORT', $request->db_port);
        }
        if ($request->filled('db_database')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_DATABASE', $request->db_database);
        }
        if ($request->filled('db_username')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_USERNAME', $request->db_username);
        }
        if ($request->filled('db_password')) {
            $envContent = $this->updateEnvVariable($envContent, 'DB_PASSWORD', $request->db_password);
        }

        return $envContent;
    }

    protected function switchToFileDrivers($envContent)
    {
        // Update .env variables
        $envContent = $this->updateEnvVariable($envContent, 'SESSION_DRIVER', 'file');
        $envContent = $this->updateEnvVariable($envContent, 'CACHE_STORE', 'file');

        // Force immediate runtime configuration changes (like InstallController)
        Config::set('session.driver', 'file');
        Config::set('cache.default', 'file');

        $this->logHelper->logActivity('update', 'drivers', 'Switched to file-based session and cache drivers');

        return $envContent;
    }

    protected function switchBackToDatabaseDrivers($envContent, $originalSessionDriver, $originalCacheStore)
    {
        $envContent = $this->updateEnvVariable($envContent, 'SESSION_DRIVER', $originalSessionDriver ?: 'database');
        $envContent = $this->updateEnvVariable($envContent, 'CACHE_STORE', $originalCacheStore ?: 'database');

        $this->logHelper->logActivity('update', 'drivers', 'Switched back to database-based session and cache drivers');

        return $envContent;
    }

    protected function runMigrationsOnNewDatabase()
    {
        try {
            // Temporarily store the original logHelper to disable logging during migration
            $originalLogHelper = $this->logHelper;

            // Create a null logger that doesn't actually log to database
            $this->logHelper = new class {
                public function logActivity($action, $module, $description)
                {
                    // Log to file instead of database during migration
                    Log::info("Activity (not logged to DB): {$action} - {$module} - {$description}");
                }
            };

            // Clear all caches first (like InstallController does)
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');

            // Purge and reconnect to database
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Verify connection to new database
            $databaseName = DB::connection()->getDatabaseName();
            Log::info("Connected to database: " . $databaseName);

            // Set longer timeout for migration (like InstallController)
            ini_set('max_execution_time', 300); // 5 minutes
            ini_set('memory_limit', '512M'); // Increase memory limit

            // Use migrate:fresh to ensure clean migration (like InstallController)
            // This will drop all tables and recreate them
            $migrateOutput = Artisan::call('migrate:fresh', ['--force' => true]);

            // Check if migrate command was successful
            if ($migrateOutput !== 0) {
                throw new Exception('Migration command failed with exit code: ' . $migrateOutput);
            }

            // Get migration output for debugging
            $output = Artisan::output();
            Log::info("Migration output: " . $output);

            // Verify critical tables exist after migration (but don't seed them)
            $this->verifyMigrationTables();

            // Now restore data from backup using RecoveryController
            $this->restoreDataFromBackup();

            // Restore the original logHelper after data is restored
            $this->logHelper = $originalLogHelper;

            // Now safe to log because users table has data
            $this->logHelper->logActivity('run', 'database_migration', 'Database migration and data restoration completed successfully');
        } catch (Exception $e) {
            // Restore original logHelper in case of error
            if (isset($originalLogHelper)) {
                $this->logHelper = $originalLogHelper;
            }

            Log::error("Migration failed: " . $e->getMessage());
            throw new Exception('Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify that critical tables exist after migration (structure only)
     */
    protected function verifyMigrationTables()
    {
        $criticalTables = [
            'users',
            'sessions',
            'cache',
            'migrations',
            'roles',
            'permissions',
            'positions',
            'profiles',
            'news',
            'galleries',
            'categories'
        ];

        $missingTables = [];

        foreach ($criticalTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            throw new Exception('Critical tables missing after migration: ' . implode(', ', $missingTables));
        }

        Log::info('All critical table structures verified successfully');
    }

    /**
     * Restore database from SQL dump file
     */
    protected function restoreDatabaseFromSql($sqlFile)
    {
        try {
            // Increase limits for large database restoration
            ini_set('max_execution_time', 600); // 10 minutes
            ini_set('memory_limit', '1G'); // 1GB memory

            $dbConnection = config('database.default');
            $dbConfig = config('database.connections.' . $dbConnection);

            // Disable foreign key checks and other optimizations
            $optimizations = [
                'SET FOREIGN_KEY_CHECKS=0;',
                'SET UNIQUE_CHECKS=0;',
                'SET AUTOCOMMIT=0;',
                'SET SQL_LOG_BIN=0;'
            ];

            foreach ($optimizations as $sql) {
                DB::statement($sql);
            }

            Log::info("Starting database restoration from: " . $sqlFile);

            // Use MySQL command line for better performance
            $passwordFlag = !empty($dbConfig['password'])
                ? "-p" . escapeshellarg($dbConfig['password'])
                : "";

            $command = sprintf(
                'mysql -h%s -u%s %s --single-transaction --routines --triggers %s < %s 2>&1',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['username']),
                $passwordFlag,
                escapeshellarg($dbConfig['database']),
                escapeshellarg($sqlFile)
            );

            Log::info("Executing command: " . str_replace($dbConfig['password'] ?? '', '***', $command));

            $output = [];
            $returnVar = 0;

            // Execute with longer timeout
            $timeStart = microtime(true);
            exec($command, $output, $returnVar);
            $timeEnd = microtime(true);

            $executionTime = round($timeEnd - $timeStart, 2);
            Log::info("Database restoration completed in {$executionTime} seconds");

            if ($returnVar !== 0) {
                $errorOutput = implode("\n", $output);
                Log::error("Database import failed with return code {$returnVar}: " . $errorOutput);
                throw new Exception('Database import failed: ' . $errorOutput);
            }

            // Re-enable foreign key checks
            $cleanup = [
                'SET FOREIGN_KEY_CHECKS=1;',
                'SET UNIQUE_CHECKS=1;',
                'SET AUTOCOMMIT=1;',
                'COMMIT;'
            ];

            foreach ($cleanup as $sql) {
                DB::statement($sql);
            }

            Log::info("Database restored successfully from: " . $sqlFile);
        } catch (Exception $e) {
            Log::error("SQL restoration failed: " . $e->getMessage());
            throw new Exception('SQL restoration failed: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup temporary files (copied from RecoveryController)
     */
    protected function cleanupTempFiles($path)
    {
        if (!is_dir($path)) {
            return;
        }

        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $path . '/' . $file;

            if (is_dir($filePath)) {
                $this->cleanupTempFiles($filePath);
            } else {
                try {
                    if (!unlink($filePath)) {
                        Log::warning("Failed to unlink file: $filePath");
                    }
                } catch (\Throwable $e) {
                    Log::error("Exception while deleting $filePath: " . $e->getMessage());
                }
            }
        }

        try {
            if (!rmdir($path)) {
                Log::warning("Failed to remove directory: $path");
            }
        } catch (\Throwable $e) {
            Log::error("Exception while removing directory $path: " . $e->getMessage());
        }
    }

    /**
     * Restore data from the most recent backup after migration
     */
    protected function restoreDataFromBackup()
    {
        try {
            Log::info("Starting data restoration process...");

            // Get the most recent backup
            $recoveryController = new \App\Http\Controllers\Dashboard\RecoveryController();
            $backups = $recoveryController->getBackups();

            if (empty($backups)) {
                Log::warning('No backups found to restore data from');
                return;
            }

            // Get the most recent backup (first in the sorted array)
            $latestBackup = $backups[0];
            $backupFileName = $latestBackup['path'];

            Log::info("Restoring data from backup: " . $backupFileName);

            // Use RecoveryController's restore method directly
            try {
                // Call the restore method from RecoveryController
                $recoveryController->restore($backupFileName);

                Log::info("Data restoration completed successfully using RecoveryController");
            } catch (Exception $e) {
                // If RecoveryController restore fails, log the error
                Log::error("RecoveryController restore failed: " . $e->getMessage());
                throw new Exception('Failed to restore backup using RecoveryController: ' . $e->getMessage());
            }
        } catch (Exception $e) {
            Log::error("Data restoration failed: " . $e->getMessage());
            throw new Exception('Data restoration failed: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
