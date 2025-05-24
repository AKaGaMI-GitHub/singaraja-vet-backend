<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
                $data = $data->where(function($query) use ($keyword) {
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

            $data = $data->paginate(8);

            Log::info('Berhasil mendapatkan data user');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data user');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data User! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
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
    public function storeOrUpdate(Request $request)
    {
        try {
            $validate = $request->validate([
                'username' => 'unique:users|required|string|min:5|max:20',
                'nama_depan' => 'required',
                'nama_belakang' => 'nullable',
                'email' => 'unique:users|email',
                'password' => 'required|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'is_vet' => 'nullable|in:0,1',
                'is_active' => 'nullable|in:0,1',
                'alamat' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:male,female',
                'mobile_code' => 'required|max:5',
                'mobile' => 'required',
            ]);
    
            $data = [
                'username' => $validate['username'],
                'nama_depan' => $validate['nama_depan'],
                'nama_belakang' => $validate['nama_belakang'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'is_active' => $validate['is_active'],
                'is_vet' => $validate['is_vet'] ?? '0'
            ];
    
            if ($request->hasFile('avatar')) {
                $img = $validate['avatar']->store('user/avatar', 'public');
                $data['avatar'] = 'storage' . $img;
            }
    
            User::updateOrCreate(['email' => $validate['email']], $data);
    
            $user = User::where('username', $validate['username'])->first();
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

            Log::info('Berhasil Membuat Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Membuat Account! (Admin)', ['user' => $data, 'detail' => $dataDetail], '1');
            return APIHelpers::responseAPI(['message' => ['user' => $data, 'detail' => $dataDetail]], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Membuat Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }

    }

    public function status($id) {
        try {
            $data = User::with('user_detail')->findOrFail($id);
            $data->update([
                'is_active' => $data->is_active == 0 ? 1 : 0
            ]);
            Log::info('Berhasil Mengganti Status Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Mengganti Status Account! (Admin)', $data, '1');
            return APIHelpers::responseAPI(['message' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Mengganti Status Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = User::with('user_detail')->findOrFail($id);
            $data->delete();
            Log::info('Berhasil Menghapus Status Account! (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil Menghapus Status Account! (Admin)', $data, '1');
            return APIHelpers::responseAPI(['message' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Menghapus Status Account! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
