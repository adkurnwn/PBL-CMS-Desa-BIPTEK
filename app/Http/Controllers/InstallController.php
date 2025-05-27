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
                'DB_CONNECTION' => 'mysql', // Change from sqlite to mysql
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username,
                'DB_PASSWORD' => $request->password,
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
        ]);

        try {
            // Create the admin user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ]);

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

            // Mark the application as installed
            $this->updateEnvFile([
                'APP_INSTALLED' => 'true',
                'APP_DEBUG' => 'false',
            ]);

            return redirect()->route('install.finish');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function finish()
    {
        Artisan::call('storage:link', ['--force' => true]);
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        return Inertia::render('Install/Finish');
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
}
