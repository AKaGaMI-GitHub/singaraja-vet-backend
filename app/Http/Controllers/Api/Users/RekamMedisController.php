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
            $data = RekamMedis::with('dokumentasi', 'hewan', 'pemilik_hewan', 'obat');

            $user = Auth::guard('sanctum')->user();

            if ($request->has('keyword')) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('nama_owner', 'like', '%' . $request->keyword . '%')
                        ->orWhere('nama_pet', 'like', '%' . $request->keyword . '%')
                        ->orWhere('diagnosa', 'like', '%' . $request->keyword . '%')
                        ->orWhere('penangangan', 'like', '%' . $request->keyword . '%');
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

                'photo.*.photo_file' => 'required|file|max:5064|mimes:jpeg,jpg,png',
                'photo.*.deskripsi' => 'nullable|string',

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
        try {
            $validate = $request->validate([
                'user_id' => 'nullable|numeric',
                'pet_id' => 'nullable|numeric',
                'nama_owner' => 'nullable|string',
                'nama_pet' => 'nullable|string',
                'diagnosa' => 'required|string',
                'penanganan' => 'required|string',

                'photo.*.photo_file' => 'nullable|file|max:5064|mimes:jpeg,jpg,png',
                'photo.*.deskripsi' => 'nullable|string',

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

            $rekamMedis = RekamMedis::findOrFail($id);

            $rekamMedis->update($data);

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

            Log::info('Berhasil update Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Berhasil update Rekam Medis', ['data' => $data, 'photo-list' => $listPhoto, 'obat-list' => $listObat], '1');
            return APIHelpers::responseAPI([
                'data' => $data,
                'photo-list' => $listPhoto,
                'obat-list' => $listObat,
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal update data Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Gagal update data Rekam Medis', ['message' => $error->getMessage()], '0');
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
            $recordRekamMedis = RekamMedis::findOrFail($id);

            $recordObat = RekamMedisObatList::where('rekam_medis_id', $id)->get();

            $photos = RekamMedisPhoto::where('rekam_medis_id', $id)->get();
            foreach ($photos as $photo) {
                if ($photo->photos && Storage::disk('public')->exists(str_replace('storage/', '', $photo->photos))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $photo->photos));
                }
                $photo->delete();
            }

            $recordRekamMedis->delete();
            $recordObat->delete();
            Log::info('Berhasil menghapus data Rekam Medis', ['id' => $id]);
            ActivityHelpers::LogActivityHelpers('Berhasil menghapus data Rekam Medis', ['pet_id' => $id], '1');

            return APIHelpers::responseAPI([
                'message' => 'Data Rekam Medis berhasil dihapus'
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal menghapus data Rekam Medis', ['error' => $error->getMessage()]);
            ActivityHelpers::LogActivityHelpers('Gagal menghapus data Rekam Medis', ['message' => $error->getMessage()], '0');

            return APIHelpers::responseAPI([
                'message' => 'Gagal menghapus data Rekam Medis',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
