<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Pets;
use App\Models\PetsPhoto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListPetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = Pets::with('owner', 'detail_photo', 'jenis_hewan', 'ras');

            $user = Auth::guard('sanctum')->user();

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

            if ($request->has('jenis_hewan')) {
                $data = $data->where('jenis_hewan_id', $request->jenis_hewan);
            }

            if ($request->has('jenis_kelamin')) {
                $data = $data->where('jenis_kelamin_pet', $request->jenis_kelamin);
            }

            if ($user->is_vet == 0) {
                $data = $data->where('user_id', $user->id);
            }

            $data = $data->orderBy('id', 'DESC')->paginate(8);

            Log::info('Berhasil mendapatkan data list Pets!');
            return APIHelpers::responseAPI($data, 200);
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
        try {
            $validate = $request->validate([
                'user_id' => 'numeric',
                'jenis_hewan_id' => 'required|numeric',
                'ras_id' => 'required|numeric',
                'nama_depan_pet' => 'required|string',
                'nama_belakang_pet' => 'nullable|string',
                'avatar_file' => 'required|file|max:2048|mimes:jpeg,jpg,png',
                'birthday' => 'required|date',
                'jenis_kelamin_pet' => 'required',
                'is_alive' => 'required|in:0,1',
                'alasan_meninggal' => 'nullable|string',
                'detail_photo.*.photos_file' => 'required|file|max:5064|mimes:jpeg,jpg,png',
            ]);

            $checkUser = Auth::guard('sanctum')->user();

            if ($checkUser->is_vet == 1) {
                $userId = $validate['user_id'];
            } else {
                $userId = $checkUser->id;
            }

            [$tahun_lahir, $bulan_lahir, $tanggal_lahir] = explode('-', $validate['birthday']);

            $data = [
                'user_id' => $userId,
                'jenis_hewan_id' => $validate['jenis_hewan_id'],
                'ras_id' => $validate['ras_id'],
                'nama_depan_pet' => $validate['nama_depan_pet'],
                'nama_belakang_pet' => $validate['nama_belakang_pet'],
                'tanggal_lahir' => $tanggal_lahir,
                'bulan_lahir' => $bulan_lahir,
                'tahun_lahir' => $tahun_lahir,
                'jenis_kelamin_pet' => $validate['jenis_kelamin_pet'],
                'is_alive' => (string) $validate['is_alive'],
                'alasan_meninggal' => $validate['alasan_meninggal'],
            ];

            if ($request->hasFile('avatar_file')) {
                $img = $validate['avatar_file']->store('user/pets/avatar', 'public');
                $data['avatar'] = 'storage/' . $img;
            }

            $storeList = Pets::create($data);

            $listPhoto = [];
            foreach ($validate['detail_photo'] as $photos) {
                $img = $photos['photos_file']->store('user/pets/photo', 'public');
                $pathImg = 'storage/' . $img;

                $photoRecord = PetsPhoto::create([
                    'pet_id' => $storeList->id,
                    'photos' => $pathImg
                ]);

                $listPhoto[] = $photoRecord;
            }

            Log::info('Berhasil store data Pets');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Pets', ['data' => $data, 'photo-list' => $listPhoto], '1');
            return APIHelpers::responseAPI([
                'data' => $data,
                'photo-list' => $listPhoto
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Pets!');
            ActivityHelpers::LogActivityHelpers('Gagal store data Pets!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showList($id)
    {
        try {
            $data = Pets::selectRaw("id as value, CONCAT(COALESCE(nama_depan_pet, ''), ' ', COALESCE(nama_belakang_pet, '')) as label")->where('user_id', $id)->get();

            Log::info('Berhasil show data Pets!');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal show data Pets!');
            ActivityHelpers::LogActivityHelpers('Gagal show data Pets!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validate = $request->validate([
                'user_id' => 'numeric',
                'jenis_hewan_id' => 'required|numeric',
                'ras_id' => 'required|numeric',
                'nama_depan_pet' => 'required|string',
                'nama_belakang_pet' => 'nullable|string',
                'avatar_file' => 'file|max:2048|mimes:jpeg,jpg,png',
                'birthday' => 'required|date',
                'jenis_kelamin_pet' => 'required',
                'is_alive' => 'required|in:0,1',
                'alasan_meninggal' => 'nullable|string',
                'detail_photo.*.photos_file' => 'file|max:5064|mimes:jpeg,jpg,png',
            ]);

            $pet = Pets::findOrFail($id);

            $checkUser = Auth::guard('sanctum')->user();

            if ($checkUser->is_vet == 1) {
                $userId = $validate['user_id'];
            } else {
                $userId = $checkUser->id;
            }

            [$tahun_lahir, $bulan_lahir, $tanggal_lahir] = explode('-', $validate['birthday']);

            $data = [
                'user_id' => $userId,
                'jenis_hewan_id' => $validate['jenis_hewan_id'],
                'ras_id' => $validate['ras_id'],
                'nama_depan_pet' => $validate['nama_depan_pet'],
                'nama_belakang_pet' => $validate['nama_belakang_pet'],
                'tanggal_lahir' => $tanggal_lahir,
                'bulan_lahir' => $bulan_lahir,
                'tahun_lahir' => $tahun_lahir,
                'jenis_kelamin_pet' => $validate['jenis_kelamin_pet'],
                'is_alive' => (string) $validate['is_alive'],
                'alasan_meninggal' => $validate['alasan_meninggal'],
            ];

            if ($request->hasFile('avatar_file')) {
                $img = $validate['avatar_file']->store('user/pets/avatar', 'public');
                $data['avatar'] = 'storage/' . $img;
            }

            $pet->update($data);

            $listPhoto = [];
            if ($request->has('detail_photo')) {
                foreach ($validate['detail_photo'] as $photoItem) {
                    if (isset($photoItem['photos_file'])) {
                        $img = $photoItem['photos_file']->store('user/pets/photo', 'public');
                        $pathImg = 'storage/' . $img;

                        $photoRecord = PetsPhoto::create([
                            'pet_id' => $pet->id,
                            'photos' => $pathImg,
                        ]);

                        $listPhoto[] = $photoRecord;
                    }
                }
            }

            Log::info('Berhasil update data Pets');
            ActivityHelpers::LogActivityHelpers('Berhasil update data Pets', ['data' => $pet, 'photo-list' => $listPhoto], '1');

            return APIHelpers::responseAPI([
                'data' => $pet,
                'photo-list' => $listPhoto,
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal update data Pets!', ['error' => $error]);
            ActivityHelpers::LogActivityHelpers('Gagal update data Pets!', ['message' => $error->getMessage()], '0');

            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pet = Pets::findOrFail($id);

            if ($pet->avatar && Storage::disk('public')->exists(str_replace('storage/', '', $pet->avatar))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $pet->avatar));
            }

            // Hapus semua foto terkait dari tabel dan storage
            $photos = PetsPhoto::where('pet_id', $id)->get();
            foreach ($photos as $photo) {
                if ($photo->photos && Storage::disk('public')->exists(str_replace('storage/', '', $photo->photos))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $photo->photos));
                }
                $photo->delete();
            }

            $pet->delete();
            Log::info('Berhasil menghapus data Pet', ['id' => $id]);
            ActivityHelpers::LogActivityHelpers('Berhasil menghapus data Pet', ['pet_id' => $id], '1');

            return APIHelpers::responseAPI([
                'message' => 'Data pet berhasil dihapus'
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal menghapus data Pet', ['error' => $error->getMessage()]);
            ActivityHelpers::LogActivityHelpers('Gagal menghapus data Pet', ['message' => $error->getMessage()], '0');

            return APIHelpers::responseAPI([
                'message' => 'Gagal menghapus data pet',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
