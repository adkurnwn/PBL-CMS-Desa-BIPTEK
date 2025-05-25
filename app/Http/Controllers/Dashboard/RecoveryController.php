<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Helpers\LogActivityHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class RecoveryController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index()
    {
        $backups = $this->getBackups();

        return Inertia::render('Dashboard/RecoveryData/Index', [
            'backups' => $backups,
        ]);
    }

    public function create()
    {
        try {
            Artisan::call('backup:run');

            $this->logHelper->logActivity('create', 'backup', 'Created new database backup');

            return redirect()->route('dashboard.recovery.index')
                ->with('success', 'Backup created successfully');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    public function download($fileName)
    {
        try {
            $backupPath = storage_path('app/backup-temp/' . $fileName);

            if (!file_exists($backupPath)) {
                $backupPath = storage_path('app/Data Recovery/' . $fileName);
            }

            if (!file_exists($backupPath)) {
                return redirect()->route('dashboard.recovery.index')
                    ->with('error', 'Backup file not found');
            }

            $this->logHelper->logActivity('download', 'backup', 'Downloaded backup file: ' . $fileName);

            return response()->download($backupPath, $fileName);
        } catch (\Exception $e) {
            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }

    public function restore($fileName)
    {
        try {
            $backupPath = storage_path('app/backup-temp/' . $fileName);

            if (!file_exists($backupPath)) {
                $backupPath = storage_path('app/Data Recovery/' . $fileName);
            }

            if (!file_exists($backupPath)) {
                return redirect()->route('dashboard.recovery.index')
                    ->with('error', 'Backup file not found');
            }

            $extractPath = storage_path('app/backup-restore-temp');
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            } else {
                $this->cleanupTempFiles($extractPath);
            }

            $zip = new \ZipArchive;
            if ($zip->open($backupPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception('Unable to extract backup archive');
            }

            $sqlFiles = glob($extractPath . '/db-dumps/*.sql');

            if (!empty($sqlFiles)) {
                $sqlFile = $sqlFiles[0]; // Use the first SQL file found

                $dbConnection = config('database.default');
                $dbConfig = config('database.connections.' . $dbConnection);

                $passwordFlag = $dbConfig['password'] !== ''
                    ? "-p" . $dbConfig['password']
                    : "";

                $command = sprintf(
                    'mysql -h%s -u%s %s %s < %s',
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['username']),
                    $passwordFlag,
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($sqlFile)
                );

                $output = [];
                $returnVar = 0;
                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception('Database import failed: ' . implode("\n", $output));
                }
            }

            $publicBackupPath = $extractPath . '/public';
            if (is_dir($publicBackupPath)) {
                $this->copyDirectory($publicBackupPath, public_path());

                $this->logHelper->logActivity('restore', 'backup', 'Restored public files from backup: ' . $fileName);
            }

            $this->cleanupTempFiles($extractPath);

            return redirect()->route('dashboard.recovery.index')
                ->with('success', 'Backup restored successfully');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Failed to restore backup: ' . $e->getMessage());
        }
    }

    public function delete($fileName)
    {
        try {
            $backupPath = storage_path('app/backup-temp/' . $fileName);

            if (!file_exists($backupPath)) {
                $backupPath = storage_path('app/Data Recovery/' . $fileName);
            }

            if (file_exists($backupPath) && unlink($backupPath)) {
                $this->logHelper->logActivity('delete', 'backup', 'Deleted backup file: ' . $fileName);
                return redirect()->route('dashboard.recovery.index')
                    ->with('success', 'Backup deleted successfully');
            }

            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Backup file not found or could not be deleted');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    private function getBackups()
    {
        $backups = [];

        $tempFiles = glob(storage_path('app/backup-temp/*.zip'));

        $laravelFiles = glob(storage_path('app/Data Recovery/*.zip'));

        $files = array_merge($tempFiles ?: [], $laravelFiles ?: []);

        foreach ($files as $file) {
            $fileName = basename($file);
            $backups[] = [
                'path' => $fileName,
                'date' => Carbon::createFromTimestamp(filemtime($file))->format('Y-m-d H:i:s'),
                'size' => $this->formatBytes(filesize($file))
            ];
        }

        usort($backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $backups;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function cleanupTempFiles($path)
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

    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $directory = opendir($source);
        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
        closedir($directory);
    }
}
