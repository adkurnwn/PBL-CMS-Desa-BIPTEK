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

            // Update the .env file with database settings
            $this->updateEnvFile([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username,
                'DB_PASSWORD' => $request->password,
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
            ]);

            // Run migrations on the newly configured MySQL database
            Artisan::call('config:clear');
            Artisan::call('migrate:fresh', ['--force' => true, '--database' => 'mysql']);
            Artisan::call('db:seed', ['--force' => true]);

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

        // Mark the application as fully installed here
        $this->updateEnvFile([
            'APP_INSTALLED' => 'true',
        ]);

        Artisan::call('storage:link', ['--force' => true]);
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'logo' => 'required|image|max:2048|mimes:jpg,jpeg,png',
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'hero_images' => 'required|array',
            'hero_images.*' => 'image|max:2048|mimes:jpg,jpeg,png',
            'organization_image' => 'required|image|max:2048|mimes:jpg,jpeg,png',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
        ]);

        try {
            // Upload logo
            $logoPath = $request->file('logo')->store('profile-logo', 'public');

            // Upload organization image
            $organizationImagePath = $request->file('organization_image')->store('organization-images', 'public');

            // Upload hero images
            $heroImages = [];
            foreach ($request->file('hero_images') as $image) {
                $heroImages[] = $image->store('hero-images', 'public');
            }

            // Create profile
            $profile = \App\Models\Profile::create([
                'id' => 1,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'logo' => $logoPath,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'hero_images' => json_encode($heroImages),
                'organization_image' => $organizationImagePath,
                'lembaga' => json_encode([]),
                'fasilitas' => json_encode([]),
                'history' => $request->history,
                'vision' => $request->vision,
                'mission' => $request->mission,
                'primary_color' => '#000000',
                'secondary_color' => '#000000',
            ]);

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
}
