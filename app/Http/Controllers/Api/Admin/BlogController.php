<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\LogActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            
        } catch (Exception $error) {
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], $error->getCode());
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
                'judul' => 'required',
                'content' => 'required',
                'tags' => 'required',
            ]);

            Blog::create([
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'views' => 0,
                'likes' => 0
            ]);

            ActivityHelpers::LogActivityHelpers('Membuat Blog', [
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'views' => 0,
                'likes' => 0
            ], '1');

            return APIHelpers::responseAPI([
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
                'views' => 0,
                'likes' => 0
            ], 200);
        } catch (Exception $error) {
            ActivityHelpers::LogActivityHelpers('Membuat Blog', [
                'error' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], $error->getCode());
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

            Blog::findOrFail($id)->update([
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
            ]);

            ActivityHelpers::LogActivityHelpers('Mengedit Blog', [
                'id' => $id,
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
            ], '1');

            return APIHelpers::responseAPI([
                'id' => $id,
                'judul' => $validate['judul'],
                'content' => $validate['content'],
                'tags' => $validate['tags'],
            ], 200);
        } catch (Exception $error) {
            ActivityHelpers::LogActivityHelpers('Mengedit Blog', [
                'error' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            
            $data = Blog::findOrFail($id);
            
            $action = $data->delete();

            ActivityHelpers::LogActivityHelpers('Menghapus Blog', $data, '1');

            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            ActivityHelpers::LogActivityHelpers('Menghapus Blog', [
                'error' => $error->getMessage()
            ], '0');

            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], $error->getCode());
        }
    }
}
