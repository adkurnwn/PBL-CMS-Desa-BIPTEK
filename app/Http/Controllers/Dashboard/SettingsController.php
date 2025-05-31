<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaService;
use App\Http\Requests\Validation\UpdateSettingsRequest;
use App\Http\Requests\Validation\UpdateThemeRequest;
use App\Models\Profile;

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

        // Update .env variables
        $envContent = $this->updateEnvVariable($envContent, 'APP_NAME', $request->app_name);
        $envContent = $this->updateEnvVariable($envContent, 'APP_TIMEZONE', $request->app_timezone);
        $envContent = $this->updateEnvVariable($envContent, 'APP_URL', $request->app_url);

        // Database settings
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

        // Write content back to .env file
        File::put($envFilePath, $envContent);

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

        return redirect()->back()->with('success', 'Settings updated successfully');
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
        $this->logHelper->logActivity('update', 'theme_settings', 'Updated theme settings');

        return redirect()->back()->with('success', 'Theme settings updated successfully');
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
}
