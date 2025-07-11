<?php


namespace App\Http\Helpers;

use Illuminate\Support\Facades\File;

class ImageHelpers
{
    public static function ImageCheckerHelpers($path)
    {
        if (!$path) {
            return asset('img/not-found-image.png');
        }

        // Hilangkan prefix 'storage/' kalau ada
        $relativePath = preg_replace('#^storage/#', '', $path);

        $fullPath = storage_path('app/public/' . $relativePath);

        if (!File::exists($fullPath)) {
            return asset('img/not-found-image.png');
        }

        return asset('storage/' . $relativePath);
    }
}
