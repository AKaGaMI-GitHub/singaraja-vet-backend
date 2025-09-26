<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\RekamMedis;
use App\Models\RekamMedisObatList;
use App\Models\RekamMedisPhoto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RekamMedisController extends Controller
{
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
            DB::beginTransaction();
            $validate = $request->validate([
                'user_id' => 'nullable|numeric',
                'pet_id' => 'nullable|numeric',
                'jenis_hewan_id' => 'nullable|numeric',
                'ras_id' => 'nullable|numeric',
                'nama_owner' => 'nullable|string',
                'nama_pet' => 'nullable|string',
                'diagnosa' => 'required|string',
                'penanganan' => 'required|string',
                'obat' => 'required',
                'dokumentasi' => 'array',
                'dokumentasi.*.photos_file' => 'required|file|max:51200|mimes:jpeg,jpg,png',
                'dokumentasi.*.deskripsi' => 'nullable|string',

            ]);

            $vetID = Auth::guard('sanctum')->user()->id;

            $data = [
                'user_id' => $validate['user_id'] ?? null,
                'pet_id' => $validate['pet_id'] ?? null,
                'jenis_hewan_id' => $validate['pet_id'] ? null : $validate['jenis_hewan_id'],
                'ras_id' => $validate['pet_id'] ? null : $validate['ras_id'],
                'nama_owner' => $validate['user_id'] ? null : $validate['nama_owner'],
                'nama_pet' => $validate['pet_id'] ? null : $validate['nama_pet'],
                'diagnosa' => $validate['diagnosa'],
                'penanganan' => $validate['penanganan'],
                'obat' => $validate['obat'],
                'vet_id' => $vetID
            ];

            $rekamMedis = RekamMedis::create($data);

            $listPhoto = [];
            $dokumentasiFiles = $request->file('dokumentasi');
            if (is_array($dokumentasiFiles)) {
                foreach ($dokumentasiFiles as $index => $fileGroup) {
                    if (isset($fileGroup['photos_file'])) {
                        $img = $fileGroup['photos_file']->store('user/rekam_medis/dokumentasi', 'public');
                        $pathImg = 'storage/' . $img;

                        $deskripsi = $request->input("dokumentasi.$index.deskripsi") ?? null;

                        $photo = RekamMedisPhoto::create([
                            'rekam_medis_id' => $rekamMedis->id,
                            'photos' => $pathImg,
                            'deskripsi' => $deskripsi
                        ]);

                        $listPhoto[] = $photo;
                    }
                }
            }

            Log::info('Berhasil store Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Berhasil store Rekam Medis', ['data' => $data, 'photo-list' => $listPhoto], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data,
                'photo-list' => $listPhoto,
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Rekam Medis');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal store data Rekam Medis', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
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
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'user_id' => 'nullable|numeric',
                'pet_id' => 'nullable|numeric',
                'jenis_hewan_id' => 'nullable|numeric',
                'ras_id' => 'nullable|numeric',
                'nama_owner' => 'nullable|string',
                'nama_pet' => 'nullable|string',
                'diagnosa' => 'required|string',
                'penanganan' => 'required|string',
                'obat' => 'required',

                'photo.*.photos_file' => 'nullable|file|max:51200|mimes:jpeg,jpg,png',
                'photo.*.deskripsi' => 'nullable|string',
            ]);

            $vetID = Auth::guard('sanctum')->user()->id;

            $data = [
                'user_id' => $validate['user_id'],
                'pet_id' => $validate['pet_id'],
                'jenis_hewan_id' => $validate['pet_id'] ? null : $validate['jenis_hewan_id'],
                'ras_id' => $validate['pet_id'] ? null : $validate['ras_id'],
                'nama_owner' => $validate['user_id'] ? null : $validate['nama_owner'],
                'nama_pet' => $validate['pet_id'] ? null : $validate['nama_pet'],
                'diagnosa' => $validate['diagnosa'],
                'penanganan' => $validate['penanganan'],
                'obat' => $validate['obat'],
                'vet_id' => $vetID
            ];

            $rekamMedis = RekamMedis::findOrFail($id);

            $rekamMedis->update($data);

            $listPhoto = [];
            if ($request->has('photo')) {
                foreach ($validate['photo'] as $photos) {
                    if ($photos['photos_file']) {
                        $img = $photos['photos_file']->store('user/rekam_medis/dokumentasi', 'public');
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

            Log::info('Berhasil update Rekam Medis');
            ActivityHelpers::LogActivityHelpers('Berhasil update Rekam Medis', ['data' => $data, 'photo-list' => $listPhoto], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data,
                'photo-list' => $listPhoto,
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal update data Rekam Medis');
            DB::rollBack();
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
            DB::beginTransaction();
            $recordRekamMedis = RekamMedis::findOrFail($id);


            $photos = RekamMedisPhoto::where('rekam_medis_id', $id)->get();
            foreach ($photos as $photo) {
                if ($photo->photos && Storage::disk('public')->exists(str_replace('storage/', '', $photo->photos))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $photo->photos));
                }
                $photo->delete();
            }

            $recordRekamMedis->delete();
            Log::info('Berhasil menghapus data Rekam Medis', ['id' => $id]);
            ActivityHelpers::LogActivityHelpers('Berhasil menghapus data Rekam Medis', ['pet_id' => $id], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'message' => 'Data Rekam Medis berhasil dihapus'
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal menghapus data Rekam Medis', ['error' => $error->getMessage()]);
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal menghapus data Rekam Medis', ['message' => $error->getMessage()], '0');

            return APIHelpers::responseAPI([
                'message' => 'Gagal menghapus data Rekam Medis',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
