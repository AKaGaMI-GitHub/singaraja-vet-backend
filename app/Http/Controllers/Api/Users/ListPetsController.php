<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Pets;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ListPetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = Pets::with('owner', 'detail_photo');

            $user = Auth::guard('sanctum')->user;

            if ($request->has('keyword')) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('nama_depan_pet', 'like', '%' . $request->keyword . '%')
                        ->orWhere('nama_belakang_pet', 'like', '%' . $request->keyword . '%');
                });
            }

            if ($request->has('owner')) {
                $data = $data->where('user_id', $request->owner);
            }

            if ($request->has('is_alive')) {
                $data = $data->where('is_alive', $request->is_alive);
            }

            if ($request->has('jenis_kelamin')) {
                $data = $data->where('jenis_kelamin_pet', $request->jenis_kelamin);
            }

            Log::info('Berhasil mendapatkan data list Pets!');
            return APIHelpers::responseAPI(['message' => 'Berhasil mendapatkan data list Pets!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data list Pets!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data list Pets!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
