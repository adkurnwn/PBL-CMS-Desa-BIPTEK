<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Helpers\LogActivityHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $existingBackups = $this->getBackups();

            foreach ($existingBackups as $backup) {
                $backupPath = storage_path('app/backup-temp/' . $backup['path']);

                if (!file_exists($backupPath)) {
                    $backupPath = storage_path('app/Data Recovery/' . $backup['path']);
                }

                if (file_exists($backupPath)) {
                    unlink($backupPath);
                    $this->logHelper->logActivity('delete', 'backup', 'Automatically deleted old backup file: ' . $backup['path']);
                }
            }

            Artisan::call('backup:run');

            $this->logHelper->logActivity('create', 'backup', 'Created new database backup');

            return redirect()->route('dashboard.recovery.index')
                ->with('success', 'Cadangan berhasil dibuat (cadangan sebelumnya telah dihapus secara otomatis)');
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

    public function restore($fileName, $returnResponse = true)
    {
        try {
            $backupPath = storage_path('app/backup-temp/' . $fileName);

            if (!file_exists($backupPath)) {
                $backupPath = storage_path('app/Data Recovery/' . $fileName);
            }

            if (!file_exists($backupPath)) {
                $error = 'Backup file not found';
                if ($returnResponse) {
                    return redirect()->route('dashboard.recovery.index')
                        ->with('error', $error);
                } else {
                    throw new \Exception($error);
                }
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
                $sqlFile = $sqlFiles[0];

                Log::info("Reading SQL file: " . $sqlFile);

                // Read the SQL file content
                $sql = file_get_contents($sqlFile);

                if ($sql === false) {
                    throw new \Exception('Unable to read SQL file: ' . $sqlFile);
                }

                Log::info("SQL file size: " . strlen($sql) . " characters");

                try {
                    // Disable foreign key checks and autocommit
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    DB::statement('SET AUTOCOMMIT=0');
                    DB::beginTransaction();

                    // Split SQL into individual statements and execute
                    $statements = preg_split('/;\s*$/m', $sql);
                    $executedCount = 0;

                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement) && !preg_match('/^(\/\*|\-\-)/', $statement)) {
                            try {
                                DB::unprepared($statement . ';');
                                $executedCount++;
                            } catch (\Exception $e) {
                                Log::warning("Failed to execute statement: " . substr($statement, 0, 100) . "... Error: " . $e->getMessage());
                            }
                        }
                    }

                    DB::commit();
                    Log::info("Database restore completed successfully. Executed {$executedCount} statements from: " . $sqlFile);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                } finally {
                    // Re-enable foreign key checks and autocommit
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    DB::statement('SET AUTOCOMMIT=1');
                }
            } else {
                Log::warning("No SQL files found in backup archive");
                Log::info("Contents of backup extraction directory: " . print_r(glob($extractPath . '/*'), true));
            }

            $publicBackupPath = $extractPath . '/public';
            if (is_dir($publicBackupPath)) {
                $this->copyDirectory($publicBackupPath, public_path());

                // Only log if we're not in a migration context (to avoid FK issues)
                if ($returnResponse && Auth::check()) {
                    $this->logHelper->logActivity('restore', 'backup', 'Restored public files from backup: ' . $fileName);
                }
            }

            $this->cleanupTempFiles($extractPath);

            if ($returnResponse) {
                return redirect()->route('dashboard.recovery.index')
                    ->with('success', 'Cadangan berhasil dipulihkan');
            } else {
                return true; // Success indicator for programmatic calls
            }
        } catch (\Exception $e) {
            Log::error("Backup restoration failed: " . $e->getMessage());
            Log::error("Exception trace: " . $e->getTraceAsString());

            if ($returnResponse) {
                return redirect()->route('dashboard.recovery.index')
                    ->with('error', 'Failed to restore backup: ' . $e->getMessage());
            } else {
                throw $e; // Re-throw for programmatic calls
            }
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
                    ->with('success', 'Cadangan berhasil dihapus');
            }

            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Backup file not found or could not be deleted');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.recovery.index')
                ->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    public function getBackups()
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
