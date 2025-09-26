<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\BlogComment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $data = Blog::with('author');

            if ($request->has('keyword')) {
                $data = $data->where('judul', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('tahun')) {
                $data = $data->where('created_at', $request->tahun);
            }

            if ($request->has('author')) {
                $data = $data->where('author_id', $request->author);
            }

            Log::info('Berhasil Mendapatkan data Blog! (Admin)');

            $data = $data->paginate(8);
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Blog!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Blog! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'judul' => 'required|unique:blogs',
                'content' => 'required',
                'tags' => 'required',
                'thumbnail_file' => 'required|mimes:jpeg,jpg,png|max:5048',
            ]);

            $data = [
                'user_id' => Auth::guard('sanctum')->id(),
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'views' => 0,
                'slug' => Str::slug($validate['judul'])
            ];

            if ($request->file('thumbnail_file')) {
                $fileThumbnail = $validate['thumbnail_file']->store('/blog/thumbnail', 'public');
                $data['thumbnail'] = $fileThumbnail;
            }

            Blog::create($data);

            ActivityHelpers::LogActivityHelpers('Membuat Blog', $data, '1');
            DB::commit();
            return APIHelpers::responseAPI(['message' => 'Berhasil membuat Blog!', 'data' => $data], 200);
        } catch (Exception $error) {
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal Membuat Blog!', [
                'message' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $data = Blog::with('komentar', 'author')->where('slug', $slug)->first();
            Log::info('Berhasil mendapatkan detail Blog! (Admin)');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan detail Blog! (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail Blog! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'judul' => 'required',
                'content' => 'required',
                'tags' => 'required',
                'thumbnail_file' => 'nullable|mimes:jpeg,jpg,png|max:5048',
            ]);

            $data = [
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'slug' => Str::slug($validate['judul'])
            ];

            if ($request->file('thumbnail_file')) {
                $fileThumbnail = $validate['thumbnail_file']->store('/blog/thumbnail', 'public');
                $data['thumbnail'] = $fileThumbnail;
            }

            $blog = Blog::where('slug', $slug)->first();

            if (!$blog) {
                ActivityHelpers::LogActivityHelpers('Gagal merubah Blog!', [
                    'message' => 'Record dengan slug ' . $slug . 'tidak ditemukan'
                ], '0');

                return APIHelpers::responseAPI([
                    'message' => 'Record dengan slug ' . $slug . 'tidak ditemukan'
                ], 500);
            }

            $blog->update($data);

            $data['id'] = $blog->id;

            ActivityHelpers::LogActivityHelpers('Mengedit Blog', $data, '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'message' => 'Berhasil merubah Blog!',
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal merubah Blog!', [
                'message' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        try {
            DB::beginTransaction();
            $data = Blog::where('slug', $slug)->first();

            if (!$data) {
                ActivityHelpers::LogActivityHelpers('Gagal merubah Blog!', [
                    'message' => 'Record dengan slug ' . $slug . 'tidak ditemukan'
                ], '0');

                return APIHelpers::responseAPI([
                    'message' => 'Record dengan slug ' . $slug . 'tidak ditemukan'
                ], 500);
            }
            $commentBlog = BlogComment::where('blog_id', $data->id)->first();
            if ($commentBlog) {
                $commentBlog->delete();
            }
            $data->delete();
            ActivityHelpers::LogActivityHelpers('Menghapus Blog', $data, '1');
            DB::commit();
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Menghapus Blog', [
                'message' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
