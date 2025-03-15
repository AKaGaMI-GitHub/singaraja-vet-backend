<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function getBlog($type = 'newest', Request $request) {
        try {
            $data = Blog::with('komentar');

            if ($type === 'newest') {
                $data = $data->orderBy('created_at', 'DESC')->limit(6);
            } else if ($type === 'list') {
                if ($request->keyword) {
                    $data = $data->where('judul', 'like', '%' . $request->keyword . '%');
                }
                if ($request->date) {
                    $data = $data->whereDate('created_at', $request->date);
                }

                if ($request->tags) {
                    $data = $data->where('tags', 'like', '%' . $request->tags . '%');
                }

                if ($request->username) {
                    $checkUsername = User::where('username', $request->username)->first();
                    if ($checkUsername) {
                        $data = $data->where('user_id', $checkUsername->id);
                    } else {
                        Log::error('Gagal mendapatkan data Blog!');
                        ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Blog!', ['message' => 'Username tidak tersedia!'], '0');
                        return APIHelpers::responseAPI(['message' => 'Username tidak tersedia!'], 500);
                    }
                }

            } else {
                Log::error('Gagal mendapatkan data Blog!');
                ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Blog!', ['message' => 'Tipe data tidak valid!'], '0');
                return APIHelpers::responseAPI(['message' => 'Tipe data tidak valid!'], 400);
            }
            
            $data = $data->get();
            Log::info('Berhasil mendapatkan data Blog!');
            return APIHelpers::responseAPI(['message' => 'Berhasil mendapatkan data Blog!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Blog!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function getDetailBlog ($slug) {
        try {
            $data = Blog::with('komentar')->where('slug', $slug)->first();
            Log::info('Berhasil mendapatkan detail Blog!');
            return APIHelpers::responseAPI(['message' => 'Berhasil mendapatkan detail Blog!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan detail Blog!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
