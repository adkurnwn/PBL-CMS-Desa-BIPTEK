<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class InstallationHelper
{
    public static function isInstalled()
    {
        return File::exists(storage_path('installed')) || env('APP_INSTALLED') === 'true';
    }
}
