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

                // Step 2: Create backup of current database
                $this->createDatabaseBackup();

                // Step 3: Test new database connection
                $this->testDatabaseConnection($request);

                // Step 4: Update database settings in .env
                $envContent = File::get($envFilePath); // Re-read after file driver changes
                $envContent = $this->updateDatabaseSettings($envContent, $request);
                File::put($envFilePath, $envContent);

                // Step 5: Clear config cache to reload new DB settings
                Artisan::call('config:clear');

                config([
                    'database.connections.mysql.host' => env('DB_HOST'),
                    'database.connections.mysql.port' => env('DB_PORT'),
                    'database.connections.mysql.database' => env('DB_DATABASE'),
                    'database.connections.mysql.username' => env('DB_USERNAME'),
                    'database.connections.mysql.password' => env('DB_PASSWORD'),
                ]);
                // Step 6: Run migrations on new database
                $this->runMigrationsOnNewDatabase();

                // Step 7: Switch session and cache drivers back to database
                $envContent = File::get($envFilePath); // Re-read after DB changes
                $envContent = $this->switchBackToDatabaseDrivers($envContent, $originalSessionDriver, $originalCacheStore);
                File::put($envFilePath, $envContent);

                // Final config clear to apply database drivers
                Artisan::call('config:clear');
            } catch (Exception $e) {
                // If anything fails, restore original drivers and return error
                try {
                    $envContent = File::get($envFilePath);
                    $envContent = $this->switchBackToDatabaseDrivers($envContent, $originalSessionDriver ?? 'database', $originalCacheStore ?? 'database');
                    File::put($envFilePath, $envContent);
                    Artisan::call('config:clear');
                } catch (Exception $restoreException) {
                    // Log the restore failure but still show the original error
                    $this->logHelper->logActivity('error', 'settings_restore', 'Failed to restore original drivers: ' . $restoreException->getMessage());
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
                // Create profile if it doesn't exist
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
        $envContent = $this->updateEnvVariable($envContent, 'SESSION_DRIVER', 'file');
        $envContent = $this->updateEnvVariable($envContent, 'CACHE_STORE', 'file');

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
            DB::purge('mysql');
            DB::reconnect('mysql');
            $databaseName = DB::connection()->getDatabaseName();
            Log::info("Connected to database: " . $databaseName);

            // Run migrations (this will create sessions and cache tables if needed)
            Artisan::call('migrate', ['--force' => true]);
            Log::info(Artisan::output());


            // Check if migrations ran successfully
            $output = Artisan::output();

            // Log migration completion
            $this->logHelper->logActivity('run', 'database_migration', 'Database migrations completed on new database');
        } catch (Exception $e) {
            throw new Exception('Migration failed: ' . $e->getMessage());
        }
    }
}
