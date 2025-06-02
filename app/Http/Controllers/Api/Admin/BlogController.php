<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $data = $data->where('nama_jenis_hewan', 'like', '%' . $request->keyword . '%');
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
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'judul' => 'required|unique:blogs',
                'content' => 'required',
                'tags' => 'required',
                'thumbnail' => 'required|mimes:jpeg,jpg,png|max:2048',
            ]);

            $data = [
                'user_id' => Auth::guard('sanctum')->id(),
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'views' => 0,
                'likes' => 0,
                'slug' => Str::slug($validate['judul'])
            ];

            if ($request->file('thumbnail')) {
                $fileThumbnail = $validate['thumbnail']->store('/blog/thumbnail', 'public');
                $data['thumbnail'] = $fileThumbnail;
            }
            
            Blog::create($data);

            ActivityHelpers::LogActivityHelpers('Membuat Blog', $data, '1');

            return APIHelpers::responseAPI(['message' => 'Berhasil membuat Blog!', 'data' => $data], 200);
        } catch (Exception $error) {
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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        try {
            $validate = $request->validate([
                'judul' => 'required',
                'content' => 'required',
                'tags' => 'required',
            ]);

            $data = [
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'slug' => Str::slug($validate['judul'])
            ];

            Blog::findOrFail($id)->update($data);

            $data['id'] = $id;

            ActivityHelpers::LogActivityHelpers('Mengedit Blog', $data, '1');

            return APIHelpers::responseAPI([
                'message' => 'Berhasil merubah Blog!',
                'data' => $data
            ], 200);
        } catch (Exception $error) {
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
    public function destroy(string $id)
    {
        try {
            $data = Blog::findOrFail($id);
            $data->delete();
            ActivityHelpers::LogActivityHelpers('Menghapus Blog', $data, '1');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            ActivityHelpers::LogActivityHelpers('Menghapus Blog', [
                'message' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
