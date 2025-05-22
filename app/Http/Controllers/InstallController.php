<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Process;

class InstallController extends Controller
{
    public function index()
    {
        // If installation is already completed, redirect to home
        if ($this->isInstalled()) {
            return redirect('/');
        }

        // Otherwise show installation page
        return Inertia::render('Installation/Setup', [
            'requirements' => $this->checkRequirements(),
            'permissions' => $this->checkPermissions(),
        ]);
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'app_name' => 'required|string',
            'app_url' => 'required|url',
            'db_connection' => 'required|in:mysql,pgsql',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8',
            'setup_nginx' => 'boolean',
            'nginx_server_name' => 'required_if:setup_nginx,1|string',
        ]);

        try {
            // Connect to database to verify credentials
            $connection = $validated['db_connection'];
            $host = $validated['db_host'];
            $port = $validated['db_port'];
            $database = $validated['db_database'];
            $username = $validated['db_username'];
            $password = $validated['db_password'];

            $testConnection = DB::connection()->getPdo();

            // Update .env file
            $this->updateEnvironmentFile($validated);

            // Run migrations and seeders
            Artisan::call('migrate:fresh --seed');

            // Create admin user with provided credentials
            $this->createAdminUser($validated);

            // Setup Nginx if requested
            if ($request->setup_nginx) {
                $this->setupNginxConfig($validated['nginx_server_name'], $validated['app_url']);
            }

            // Create installed file to indicate installation is complete
            File::put(storage_path('installed'), date('Y-m-d H:i:s'));

            return redirect('/')->with('success', 'Installation completed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Installation failed: ' . $e->getMessage()]);
        }
    }

    private function isInstalled()
    {
        return File::exists(storage_path('installed'));
    }

    private function checkRequirements()
    {
        return [
            'php' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'xml' => extension_loaded('xml'),
            'ctype' => extension_loaded('ctype'),
            'json' => extension_loaded('json'),
            'bcmath' => extension_loaded('bcmath'),
            'fileinfo' => extension_loaded('fileinfo'),
            'openssl' => extension_loaded('openssl'),
        ];
    }

    private function checkPermissions()
    {
        return [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => is_writable(base_path('.env')) || is_writable(base_path()),
        ];
    }

    private function updateEnvironmentFile($data)
    {
        $envPath = base_path('.env');

        // If .env.example exists but .env doesn't, create from example
        if (!file_exists($envPath) && file_exists(base_path('.env.example'))) {
            copy(base_path('.env.example'), $envPath);
        }

        // Update environment variables
        $env = file_get_contents($envPath);

        $env = $this->setEnvValue($env, 'APP_NAME', '"' . $data['app_name'] . '"');
        $env = $this->setEnvValue($env, 'APP_URL', $data['app_url']);
        $env = $this->setEnvValue($env, 'DB_CONNECTION', $data['db_connection']);
        $env = $this->setEnvValue($env, 'DB_HOST', $data['db_host']);
        $env = $this->setEnvValue($env, 'DB_PORT', $data['db_port']);
        $env = $this->setEnvValue($env, 'DB_DATABASE', $data['db_database']);
        $env = $this->setEnvValue($env, 'DB_USERNAME', $data['db_username']);
        $env = $this->setEnvValue($env, 'DB_PASSWORD', $data['db_password']);

        // Mark the application as installed
        $env = $this->setEnvValue($env, 'APP_INSTALLED', 'true');

        file_put_contents($envPath, $env);
    }

    private function setEnvValue($env, $key, $value)
    {
        $key = strtoupper($key);

        if (str_contains($env, "{$key}=")) {
            return preg_replace("/{$key}=(.*)/", "{$key}={$value}", $env);
        } else {
            return $env . "\n{$key}={$value}\n";
        }
    }

    private function createAdminUser($data)
    {
        // Create admin user based on your User model and authentication system
        // Example for a model with name, email, password fields:
        $user = \App\Models\User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => bcrypt($data['admin_password']),
            'position_id' => null,
            'photo_profile' => '/profile-photos/admin.png',
            'phone' => '081234567890',
            'status' => true,
        ]);

        // Assign admin role
        $user->assignRole('Admin');
    }

    private function setupNginxConfig($serverName, $appUrl)
    {
        // Extract domain from app URL
        $parsedUrl = parse_url($appUrl);
        $domain = $parsedUrl['host'] ?? $serverName;

        $configPath = "/etc/nginx/sites-available/{$domain}.conf";
        $enabledPath = "/etc/nginx/sites-enabled/{$domain}.conf";

        // Create Nginx config
        $config = <<<EOT
server {
    listen 80;
    server_name {$domain};
    root {$this->getPublicPath()};

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOT;

        // Check if running as sudo
        if (function_exists('posix_getuid') && posix_getuid() === 0) {
            file_put_contents($configPath, $config);

            if (!file_exists($enabledPath)) {
                symlink($configPath, $enabledPath);
            }

            // Test and reload Nginx
            Process::run('nginx -t && systemctl reload nginx');
        } else {
            // Cannot write to /etc as non-root, save to storage for manual setup
            $userConfig = storage_path("nginx-{$domain}.conf");
            file_put_contents($userConfig, $config);

            // Throw exception with instructions
            throw new \Exception(
                "Nginx configuration saved to {$userConfig}. " .
                    "Please manually copy this file to {$configPath} and enable it."
            );
        }
    }

    private function getPublicPath()
    {
        return base_path('public');
    }
}
