<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

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

            // Update the .env file with database settings AND change session driver to file temporarily
            $this->updateEnvFile([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username,
                'DB_PASSWORD' => '"' . $request->password . '"',
                'DB_COLLATION' => 'utf8mb4_unicode_ci',
                'SESSION_DRIVER' => 'file',
                'CACHE_STORE' => 'file',
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
            ]);

            // Set environment variables for immediate effect
            putenv("DB_CONNECTION=mysql");
            putenv("DB_HOST={$request->host}");
            putenv("DB_PORT={$request->port}");
            putenv("DB_DATABASE={$request->database}");
            putenv("DB_USERNAME={$request->username}");
            putenv("DB_PASSWORD={$request->password}");
            putenv("SESSION_DRIVER=file");
            putenv("CACHE_STORE=file");

            // Update configuration array directly
            config([
                'database.connections.mysql.host' => $request->host,
                'database.connections.mysql.port' => $request->port,
                'database.connections.mysql.database' => $request->database,
                'database.connections.mysql.username' => $request->username,
                'database.connections.mysql.password' => $request->password,
                'session.driver' => 'file', // Change session driver to file
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
                // Run migrations
                $migrateOutput = Artisan::call('migrate:fresh', ['--force' => true]);

                // Run core seeders first (roles, permissions)
                Artisan::call('db:seed', [
                    '--class' => 'RoleSeeder',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'PositionSeeder',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'PermissionSeeder',
                    '--force' => true
                ]);

                // Run region seeders with increased memory
                ini_set('memory_limit', '512M');
                Artisan::call('db:seed', [
                    '--class' => 'IndoRegionSeeder',
                    '--force' => true
                ]);

                // Run remaining seeders
                Artisan::call('db:seed', [
                    '--class' => 'CategorySeeder',
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
            'photo_profile' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
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
            } else {
                // Use a default profile image when none is provided
                $userData['photo_profile'] = 'profile-photos/default-admin.png';

                // Check if the default image exists in storage, if not, copy it from assets
                $defaultImagePath = public_path('storage/profile-photos/default-admin.png');
                $defaultImageDir = dirname($defaultImagePath);

                if (!file_exists($defaultImagePath)) {
                    // Create directory if it doesn't exist
                    if (!file_exists($defaultImageDir)) {
                        mkdir($defaultImageDir, 0755, true);
                    }

                    // Copy default image from assets (you may need to adjust this path)
                    $sourceImage = public_path('assets/images/default-admin.png');

                    // If source exists, copy it; otherwise use a placeholder
                    if (file_exists($sourceImage)) {
                        copy($sourceImage, $defaultImagePath);
                    } else {
                        $this->generateDefaultAvatar($request->name, $defaultImagePath);
                    }
                }
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

            // Create default settings if needed
            $this->createDefaultSettings();

            // Remove the APP_INSTALLED setting from here, just set APP_DEBUG to false
            $this->updateEnvFile([
                'APP_DEBUG' => 'false',
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
        if (\App\Models\Profile::count() === 0) {
            return redirect()->route('install.setupProfile')
                ->with('warning', 'Please complete your village profile setup before finishing installation.');
        }

        // Don't mark as installed yet - we'll do that when they click "Ke Halaman Login"
        // Keep the storage:link and cache commands
        Artisan::call('storage:link', ['--force' => true]);

        return Inertia::render('Install/Finish');
    }

    /**
     * Setup Profile Desa and handle not found errors
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255', // Make optional since we generate from village
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'logo' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            // Change validation to match frontend
            'hero_image_1' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'hero_image_2' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'hero_image_3' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'organization_image' => 'required|image|max:10000|mimes:jpg,jpeg,png',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'lembaga' => 'nullable|array', // Change to array to match frontend
            'fasilitas' => 'nullable|array', // Change to array to match frontend
        ]);

        try {
            // Get village name for profile name
            $village = \App\Models\Region\Village::find($request->kelurahan);
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
                // Handle array data from frontend - encode to JSON like ProfileController
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
            $profile = \App\Models\Profile::create($profileData);

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
            if (\App\Models\Profile::count() === 0) {
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
     * Generate a simple default avatar image with the user's initials
     *
     * @param string $name
     * @param string $outputPath
     * @return void
     */
    private function generateDefaultAvatar($name, $outputPath)
    {
        // Get initials from name (up to 2 characters)
        $initials = strtoupper(mb_substr($name, 0, 1));
        if (strpos($name, ' ') !== false) {
            $nameParts = explode(' ', $name);
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1));
        }

        // Create image
        $image = imagecreatetruecolor(200, 200);

        // Define colors
        $bgColor = imagecolorallocate($image, 36, 64, 49); // Your accent color #244031
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Add text
        $font = 5; // Built-in font size (1-5)
        $textWidth = imagefontwidth($font) * strlen($initials);
        $textHeight = imagefontheight($font);

        $centerX = (200 - $textWidth) / 2;
        $centerY = (200 - $textHeight) / 2;

        imagestring($image, $font, $centerX, $centerY, $initials, $textColor);

        // Save as PNG
        imagepng($image, $outputPath);
        imagedestroy($image);
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
