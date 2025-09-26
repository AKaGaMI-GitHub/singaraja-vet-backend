<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function getBlog($type = 'newest', Request $request)
    {
        try {
            $data = Blog::with('komentar', 'author.user_detail')
                ->select('id', 'user_id', 'judul', 'content', 'views', 'slug', 'thumbnail', 'tags', 'created_at');

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

                $data = $data->orderBy('id', 'DESC')->paginate(6);
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

    public function getDetailBlog($slug)
    {
        try {
            $data = Blog::with('komentar.user_comment', 'komentar.balasan.user_comment', 'author.user_detail')->where('slug', $slug)->first();
            $data->update([
                'views' => $data->views + 1
            ]);
            $latestBlog = Blog::with('komentar', 'author.user_detail')->orderBy('id', 'DESC')->limit(4)->get();
            Log::info('Berhasil mendapatkan detail Blog!');
            return APIHelpers::responseAPI(['message' => 'Berhasil mendapatkan detail Blog!', 'data' => $data, 'latestBlog' => $latestBlog], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan detail Blog!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function commentBlogParent($slug, Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'comment' => 'required|string|max:300'
            ]);
            $blogID = Blog::where('slug', $slug)->first()->id;
            $comment = [
                'blog_id' => $blogID,
                'user_id' => Auth::guard('sanctum')->id(),
                'comment' => $validate['comment']
            ];
            $data = BlogComment::create($comment);
            Log::info('Berhasil comment Blog!');
            ActivityHelpers::LogActivityHelpers('Berhasil comment Blog!', $data, '1');
            DB::commit();
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal comment Blog!');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal comment Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function commentBlogChildren($slug, $idParent, Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'comment' => 'required|string|max:300'
            ]);
            $blogID = Blog::where('slug', $slug)->first()->id;
            $comment = [
                'blog_id' => $blogID,
                'parent_comment_id' => $idParent,
                'user_id' => Auth::guard('sanctum')->id(),
                'comment' => $validate['comment']
            ];
            $data = BlogComment::create($comment);
            Log::info('Berhasil membalas comment Blog!');
            DB::commit();
            ActivityHelpers::LogActivityHelpers('Berhasil membalas comment Blog!', $data, '1');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal membalas comment Blog!');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal membalas comment Blog!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
