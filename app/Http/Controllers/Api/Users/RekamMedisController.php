<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\RekamMedis;
use App\Models\RekamMedisPhoto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RekamMedisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = RekamMedis::with('dokumentasi', 'hewan.jenis_hewan', 'hewan.ras', 'hewan.jenis_kelamin', 'pemilik_hewan.user_detail', 'ras', 'jenis_hewan');

            $user = Auth::guard('sanctum')->user();

            if ($request->has('keyword')) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('nama_owner', 'like', '%' . $request->keyword . '%')
                        ->orWhere('nama_pet', 'like', '%' . $request->keyword . '%')
                        ->orWhere('diagnosa', 'like', '%' . $request->keyword . '%')
                        ->orWhere('penanganan', 'like', '%' . $request->keyword . '%')
                        ->orWhereHas('pemilik_hewan', function ($q) use ($request) {
                            $q->whereRaw("CONCAT(nama_depan, ' ', nama_belakang) LIKE ?", ['%' . $request->keyword . '%']);
                        })
                        ->orWhereHas('hewan', function ($q) use ($request) {
                           $q->whereRaw("CONCAT(nama_depan_pet, ' ', nama_belakang_pet) LIKE ?", ['%' . $request->keyword . '%']);
                        });;
                });
            }

            if ($request->has('owner')) {
                $data = $data->where('user_id', $request->owner);
            }

            if ($request->has('pet')) {
                $data = $data->where('pet_id', $request->pet);
            }

            if ($user->is_vet == 0) {
                $data = $data->where('user_id', $user->id);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Rekam Medis');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Rekam Medis', ['message' => $error->getMessage()], '0');
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
    
}
