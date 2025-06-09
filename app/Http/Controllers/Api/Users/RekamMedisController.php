<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\RekamMedis;
use App\Models\RekamMedisObatList;
use App\Models\RekamMedisPhoto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RekamMedisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = RekamMedis::with('dokumentasi', 'hewan', 'pemilik_hewan', 'obat');

            if ($request->has('keyword')) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('nama_owner', 'like', '%' . $request->keyword . '%')
                        ->orWhere('nama_pet', 'like', '%' . $request->keyword . '%')
                        ->orWhere('diagnosa', 'like', '%' . $request->keyword . '%')
                        ->orWhere('penangangan', 'like', '%' . $request->keyword . '%');
                });
            }

            if ($request->has('user')) {
                $data = $data->where('user_id', $request->user);
            }

            if ($request->has('pet')) {
                $data = $data->where('pet_id', $request->pet);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Rekam Medis');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Rekam Medis', ['message' => $error->getMessage()], '0');
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'user_id' => 'nullable|numeric',
                'pet_id' => 'nullable|numeric',
                'nama_owner' => 'nullable|string',
                'nama_pet' => 'nullable|string',
                'diagnosa' => 'required|string',
                'penanganan' => 'required|string',

                // 'photo.*.rekam_medis_id' => 'required|numeric',
                'photo.*.photo_file' => 'required|file|max:5064|mimes:jpeg,jpg,png',
                'photo.*.deskripsi' => 'nullable|string',

                // 'obat.rekam_medis_id' => 'required|numeric',
                'obat.obat_id' => 'required|array',
                'obat.obat_id.*' => 'required|numeric',
            ]);

            $data = [
                'user_id' => $validate['user_id'],
                'pet_id' => $validate['pet_id'],
                'nama_owner' => $validate['nama_owner'],
                'nama_pet' => $validate['nama_pet'],
                'diagnosa' => $validate['diagnosa'],
                'penanganan' => $validate['penanganan']
            ];

            $rekamMedis = RekamMedis::create($data);

            $listPhoto = [];
            if ($request->has('photo')) {
                foreach ($validate['photo'] as $photos) {
                    if ($photos['photo_file']) {
                        $img = $photos['photo_file']->store('user/rekam_medis/dokumentasi', 'public');
                        $pathImg = 'storage/' . $img;
                        $photo = RekamMedisPhoto::create([
                            'rekam_medis_id' => $rekamMedis->id,
                            'photos' => $pathImg,
                            'deskripsi' => $photos['deskripsi']
                        ]);

                        $listPhoto[] = $photo;
                    }
                }
            }

            $listObat = [];
            if ($validate['obat_id']) {
               foreach ($validate['obat']['obat_id'] as $obatId) {
                    $obat = RekamMedisObatList::create([
                        'rekam_medis_id' => $rekamMedis->id,
                        'obat_id' => $obatId,
                    ]);
                    $listObat[] = $obat;
                }
            }

             Log::info('Berhasil store Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Berhasil store Rekam Medis', ['data' => $data, 'photo-list' => $listPhoto, 'obat-list' => $listObat], '1');
            return APIHelpers::responseAPI([
                'data' => $data,
                'photo-list' => $listPhoto,
                'obat-list' => $listObat,
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Gagal store data Rekam Medis', ['message' => $error->getMessage()], '0');
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
