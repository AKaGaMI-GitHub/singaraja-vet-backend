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

        $fullPath = storage_path('app/public/' . $path);

        if (!File::exists($fullPath)) {
            return asset('img/not-found-image.png');
        }

        return asset('storage/' . $path);
    }
}
