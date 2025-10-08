<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = User::with('user_detail', 'pets');
            if ($request->has('keyword')) {
                $keyword = '%' . $request->keyword . '%';
                $data = $data->where(function ($query) use ($keyword) {
                    $query->where('nama_depan', 'like', $keyword)
                        ->orWhere('nama_belakang', 'like', $keyword)
                        ->orWhere('email', 'like', $keyword)
                        ->orWhere('username', 'like', $keyword);
                });
            }

            if ($request->has('status')) {
                $data = $data->where('is_active', (string) $request->status);
            }

            if ($request->has('vet_status')) {
                $data = $data->where('is_vet', (string) $request->vet_status);
            }

            $data = $data->orderBy('id', 'DESC')->paginate(8);

            Log::info('Berhasil mendapatkan data user');
            DB::commit();
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data user');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data User! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function showList()
    {
        try {
            $data = User::where('is_active', '1')->get([
                'id as value',
                DB::raw("CONCAT(nama_depan, ' ', nama_belakang) as label")
            ]);
            Log::info('Berhasil mendapatkan data list user (Admin)');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data list user');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data list User! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    public function edit($username)
    {
        try {
            $data = User::with('user_detail')->where('username', $username)->first();

            Log::info('Berhasil mendapatkan data user');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data user');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data User! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrUpdate(Request $request)
    {
        try {
            $isUpdate = $request->has('username');

            $validate = $request->validate([
                'username' => $isUpdate ? "" : 'unique:users|required|string|min:5|max:20',
                'nama_depan' => 'required',
                'nama_belakang' => 'nullable',
                'email' => $isUpdate ? '' : 'unique:users|email',
                'password' => $isUpdate ? 'nullable|string' : 'required|string',
                'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
                'is_vet' => 'nullable|in:0,1',
                'alamat' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:male,female',
                'mobile_code' => 'required|max:5',
                'mobile' => 'required',
            ]);

            $data = [
                'nama_depan' => $validate['nama_depan'],
                'nama_belakang' => $validate['nama_belakang'],
                'email' => $validate['email'],
                'is_active' => '1',
                'is_vet' => (string) $validate['is_vet'] ?? '0'
            ];

            if (!$isUpdate) {
                $data['username'] = $validate['username'];
            }

            if ($request->password != '') {
                $data['password'] = Hash::make($validate['password']);
            }

            if ($request->hasFile('avatar_file')) {
                $img = $validate['avatar_file']->store('user/avatar', 'public');
                $data['avatar'] = 'storage' . $img;
            }

            User::updateOrCreate(['email' => $validate['email']], $data);

            $user = User::where('email', $validate['email'])->first();
            $userID = $user->id;

            $dataDetail = [
                'user_id' => $userID,
                'alamat' => $validate['alamat'],
                'tempat_lahir' => $validate['tempat_lahir'],
                'tanggal_lahir' => $validate['tanggal_lahir'],
                'jenis_kelamin' => (string) $validate['jenis_kelamin'],
                'mobile' => $validate['mobile_code'] . $validate['mobile'],
            ];

            UserDetail::updateOrCreate(['user_id' => $userID], $dataDetail);

            Log::info('Berhasil Memanipulasi Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Memanipulasi Account! (Admin)', ['user' => $data, 'detail' => $dataDetail], '1');
            return APIHelpers::responseAPI(['message' => 'Berhasil Mengolah Data Account!', 'data' => ['user' => $data, 'detail' => $dataDetail]], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Memanipulasi Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function status($username)
    {
        try {
            $data = User::with('user_detail')->where('username', $username)->first();
            if (!$data) {
                ActivityHelpers::LogActivityHelpers('Gagal Mengganti Status Account! (Admin)', ['message' => 'User tidak ditemukan'], '0');
                return APIHelpers::responseAPI(['message' => 'User tidak ditemukan'], 404);
            }
            $data->update([
                'is_active' => $data->is_active == 0 ? '1' : '0'
            ]);
            Log::info('Berhasil Mengganti Status Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Mengganti Status Account! (Admin)', $data, '1');
            return APIHelpers::responseAPI(['message' => 'Berhasil Mengganti Status Account!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Mengganti Status Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function vetStatus($username)
    {
        try {
            $data = User::with('user_detail')->where('username', $username)->first();
            if (!$data) {
                ActivityHelpers::LogActivityHelpers('Gagal Mengganti Status Account! (Admin)', ['message' => 'User tidak ditemukan'], '0');
                return APIHelpers::responseAPI(['message' => 'User tidak ditemukan'], 404);
            }
            $data->update([
                'is_vet' => $data->is_vet == 0 ? '1' : '0'
            ]);
            Log::info('Berhasil Mengganti Status Vet Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Mengganti Status Account! (Admin)', $data, '1');
            return APIHelpers::responseAPI(['message' => 'Berhasil Mengganti Status Vet Account!', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Mengganti Status Vet Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username)
    {
        try {
            $data = User::with('user_detail')->where('username', $username)->first();
            if (!$data) {
                ActivityHelpers::LogActivityHelpers('Gagal Menghapus Account! (Admin)', ['message' => 'User tidak ditemukan'], '0');
                return APIHelpers::responseAPI(['message' => 'User tidak ditemukan'], 404);
            }
            $data->delete();
            Log::info('Berhasil Menghapus Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Menghapus Account! (Admin)', $data, '1');
            return APIHelpers::responseAPI(['message' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Menghapus Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
