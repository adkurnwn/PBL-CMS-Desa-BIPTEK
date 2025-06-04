<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Region\Village;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    public function index()
    {
        return Inertia::render('Install/Welcome');
    }

    public function requirements()
    {
        $phpVersion = PHP_VERSION;
        $requirements = [
            'PHP Version >= 8.1' => version_compare($phpVersion, '8.1.0', '>='),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'cURL Extension' => extension_loaded('curl'),
            'GD Extension' => extension_loaded('gd'),
            'Zip Extension' => extension_loaded('zip'),
        ];

        $allMet = !in_array(false, $requirements);

        return Inertia::render('Install/Requirements', [
            'requirements' => $requirements,
            'allMet' => $allMet
        ]);
    }

    public function permissions()
    {
        $permissions = [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => is_writable(base_path('.env')),
        ];

        $allGranted = !in_array(false, $permissions);

        return Inertia::render('Install/Permissions', [
            'permissions' => $permissions,
            'allGranted' => $allGranted
        ]);
    }

    public function database()
    {
        $currentConfig = [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
        ];

        return Inertia::render('Install/Database', [
            'currentConfig' => $currentConfig,
        ]);
    }

    public function setDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|numeric',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'run_seeders' => 'boolean',
        ]);

        try {
            // Test the database connection
            $connection = $this->testConnection(
                $request->host,
                $request->port,
                $request->username,
                $request->password,
                $request->database
            );

            if (!$connection['success']) {
                return redirect()->back()->withErrors(['connection' => $connection['message']]);
            }

            // Update the .env file with database settings
            $this->updateEnvFile([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => '"' . $request->database . '"',
                'DB_USERNAME' => '"' . $request->username . '"',
                'DB_PASSWORD' => '"' . $request->password . '"',
                'DB_COLLATION' => 'utf8mb4_unicode_ci',
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE' => 'file',
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
                // Store seeder preference for later use
                'RUN_SEEDERS' => $request->input('run_seeders', true) ? 'true' : 'false',
            ]);

            // Generate application key if it doesn't exist
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Update configuration array directly
            config([
                'database.connections.mysql.host' => $request->host,
                'database.connections.mysql.port' => $request->port,
                'database.connections.mysql.database' => $request->database,
                'database.connections.mysql.username' => $request->username,
                'database.connections.mysql.password' => $request->password,
                'session.driver' => 'file',
                'cache.default' => 'file',
            ]);

            // Refresh database connection
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Clear all caches to ensure changes take effect
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');

            // Set longer timeout for migration
            ini_set('max_execution_time', 300); // 5 minutes

            try {
                // Run migrations only
                $migrateOutput = Artisan::call('migrate:fresh', ['--force' => true]);

                // Only run IndoRegionSeeder as it's required and doesn't depend on users
                ini_set('memory_limit', '512M');
                Artisan::call('db:seed', [
                    '--class' => 'IndoRegionSeeder',
                    '--force' => true
                ]);
                Artisan::call('db:seed', [
                    '--class' => 'RoleSeeder',
                    '--force' => true
                ]);
                Artisan::call('db:seed', [
                    '--class' => 'PermissionSeeder',
                    '--force' => true
                ]);

                // After migrations are complete, switch back to database session driver
                $this->updateEnvFile([
                    'SESSION_DRIVER' => 'database',
                ]);

                // Update config for immediate effect
                config(['session.driver' => 'database']);
            } catch (\Exception $migrationException) {
                return redirect()->back()->withErrors(['error' => 'Migration error: ' . $migrationException->getMessage()]);
            }

            return redirect()->route('install.admin');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function admin()
    {
        return Inertia::render('Install/Admin');
    }

    public function setAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'photo_profile' => 'required|image|max:10000|mimes:jpg,jpeg,png',
        ]);

        try {
            // Create the admin user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ];

            // Handle profile photo upload
            if ($request->hasFile('photo_profile')) {
                $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos', 'public');
            }

            $user = User::create($userData);

            // Ensure roles are created
            if (!Role::where('name', 'Admin')->exists()) {
                Artisan::call('db:seed', [
                    '--class' => 'RoleSeeder',
                    '--force' => true
                ]);
            }

            // Assign admin role to the user
            $user->assignRole('Admin');

            // Now run the remaining seeders that require user relationships
            if (env('RUN_SEEDERS', 'true') === 'true') {
                try {
                    ini_set('memory_limit', '512M');
                    ini_set('max_execution_time', 300);

                    // Run all remaining seeders (excluding IndoRegionSeeder which was already run)
                    Artisan::call('db:seed', [
                        '--force' => true,
                        '--class' => 'DatabaseSeeder' // This will run all seeders except those already run
                    ]);
                } catch (\Exception $seederException) {
                    // Log the error but don't fail the installation
                    Log::warning('Some seeders failed to run: ' . $seederException->getMessage());
                }
            }

            // Create default settings if needed
            $this->createDefaultSettings();

            // Clean up the RUN_SEEDERS env variable and set APP_DEBUG to false
            $this->updateEnvFile([
                'APP_DEBUG' => 'false',
                'RUN_SEEDERS' => null, // Remove this temporary variable
            ]);

            return redirect()->route('install.setupProfile');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function setupProfile()
    {
        return Inertia::render('Install/SetupProfile');
    }

    public function finish()
    {
        // Check if profile exists
        if (Profile::count() === 0) {
            return redirect()->route('install.setupProfile')
                ->with('warning', 'Please complete your village profile setup before finishing installation.');
        }

        // Keep the storage:link and cache commands
        Artisan::call('storage:link', ['--force' => true]);

        return Inertia::render('Install/Finish');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'logo' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kode_pos' => 'required|string|max:10',
            'hero_image_1' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'hero_image_2' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'hero_image_3' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'organization_image' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'lembaga' => 'nullable|array',
            'fasilitas' => 'nullable|array',
        ]);

        try {
            // Get village name for profile name
            $village = Village::find($request->kelurahan);
            $profileName = $village ? "Desa " . ucwords(strtolower($village->name)) : 'Desa Default';

            // Create base profile data
            $profileData = [
                'id' => 1,
                'name' => $profileName,
                'email' => $request->email,
                'phone' => $request->phone,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,
                'lembaga' => json_encode($request->lembaga ?? []),
                'fasilitas' => json_encode($request->fasilitas ?? []),
                'history' => $request->history,
                'vision' => $request->vision,
                'mission' => $request->mission,
            ];

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('profile-logo', 'public');
                $profileData['logo'] = $logoPath;
            }

            // Handle organization image
            if ($request->hasFile('organization_image')) {
                $orgImagePath = $request->file('organization_image')->store('organization-images', 'public');
                $profileData['organization_image'] = $orgImagePath;
            }

            // Handle individual hero images
            $heroImages = [];
            for ($i = 1; $i <= 3; $i++) {
                if ($request->hasFile("hero_image_$i")) {
                    $heroImages[] = $request->file("hero_image_$i")->store('hero-images', 'public');
                }
            }
            $profileData['hero_images'] = json_encode($heroImages);

            // Create the profile
            Profile::create($profileData);

            // Create log entry of this action
            $logHelper = new \App\Helpers\LogActivityHelper();
            $logHelper->logActivity('create', 'profile', 'Membuat profil desa ' . $profileName);

            // Ensure storage link is created
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link', ['--force' => true]);
            }

            return redirect()->route('install.finish');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to setup profile: ' . $e->getMessage()]);
        }
    }

    public function handleProfileNotFound($name = null)
    {
        // Check if the system has been installed but profile doesn't exist
        if (env('APP_INSTALLED') === 'true') {
            if (Profile::count() === 0) {
                // Redirect to profile setup if no profile exists
                return redirect()->route('install.setupProfile')
                    ->with('warning', 'Please complete your village profile setup first.');
            }
        }

        // If system is not installed, redirect to installation
        if (env('APP_INSTALLED') !== 'true') {
            return redirect()->route('install.index');
        }

        // Generic not found response
        return response()->view('errors.404', [
            'message' => 'The requested resource "' . ($name ?? 'Unknown') . '" was not found.',
            'suggestion' => 'Please check the URL or return to the dashboard.'
        ], 404);
    }

    private function testConnection($host, $port, $username, $password, $database)
    {
        try {
            $connection = [
                'driver' => 'mysql',
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            $pdo = new \PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            return ['success' => true];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function updateEnvFile($data)
    {
        $path = base_path('.env');
        $content = File::get($path);

        foreach ($data as $key => $value) {
            // Check if the value needs quotes (contains spaces)
            if (strpos($value, ' ') !== false && strpos($value, '"') === false) {
                $value = '"' . $value . '"';
            }

            // Update existing key
            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            }
            // Add key if it doesn't exist
            else {
                $content .= "\n{$key}={$value}";
            }
        }

        File::put($path, $content);
    }

    private function createDefaultSettings()
    {
        // Create a settings table if needed
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function ($table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'CMS Desa', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Website Resmi Desa', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Mark the installation as completely finished when user navigates to login
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finalize()
    {
        // Force set APP_INSTALLED to true in .env and switch to production mode
        $this->updateEnvFile([
            'APP_INSTALLED' => 'true',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'database',
        ]);

        // Clear all caches to ensure changes take effect
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('cache:clear');

        return redirect('/login');
    }
}
