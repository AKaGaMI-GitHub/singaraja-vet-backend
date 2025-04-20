<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function getBlog($type = 'newest', Request $request) {
        try {
            $data = Blog::with('komentar', 'author.user_detail')
                ->select('id', 'user_id', 'judul', 'content', 'views', 'likes', 'slug', 'thumbnail', 'tags', 'created_at');

            if ($type === 'newest') {
                $data = $data->orderBy('created_at', 'DESC')->limit(6)->get();
            } else if ($type === 'list') {
                if ($request->keyword) {
                    $data = $data->where('judul', 'like', '%' . $request->keyword . '%');
                }

                if ($request->from && $request->to) {
                    $data = $data->whereBetween('created_at', [$request->from, $request->to]);
                } elseif ($request->from) {
                    $data = $data->whereDate('created_at', '>=', $request->from);
                } elseif ($request->to) {
                    $data = $data->whereDate('created_at', '<=', $request->to);
                }
                
                if ($request->tags) {
                    $tags = explode(',', $request->tags);
                    $data = $data->where(function ($query) use ($tags) {
                        foreach ($tags as $tag) {
                            $query->orWhere('tags', 'like', '%' . $tag . '%');
                        }
                    });
                }
                
                $data = $data->paginate(15);
            } else {
                Log::error('Gagal mendapatkan data Blog!');
                ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Blog!', ['message' => 'Tipe data tidak valid!'], '0');
                return APIHelpers::responseAPI(['message' => 'Tipe data tidak valid!'], 400);
            }
            
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
            $data = Blog::with('komentar', 'author.user_detail')->where('slug', $slug)->first();
            Log::info('Berhasil mendapatkan detail Blog!');
            return APIHelpers::responseAPI(['message' => 'Berhasil mendapatkan detail Blog!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan detail Blog!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
