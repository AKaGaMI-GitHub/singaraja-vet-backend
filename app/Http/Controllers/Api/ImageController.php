<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function checkImage($path)
    {
        try {
            if (!File::exists($path)) {
                return response()->file(public_path('img/not-found-image.png'));
            }

            return response()->file($path);
        } catch (Exception $error) {
            Log::error('Gagal mengecheck Gambar!');
            ActivityHelpers::LogActivityHelpers('Gagal mengecheck Gambar!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
