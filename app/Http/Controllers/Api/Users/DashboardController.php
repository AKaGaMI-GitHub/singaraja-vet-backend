<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Blog;
use App\Models\Pets;
use App\Models\RekamMedis;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $totalHewan = Pets::query();
            $pemeriksaan = RekamMedis::query();
            $totalBlog = Blog::select('id')->count();

            if ($user->is_vet == 0) {
                $totalHewan = $totalHewan->where('user_id', $user->id);
                $pemeriksaan = $pemeriksaan->where('user_id', $user->id);
            }

            $totalHewan = $totalHewan->count();
            $totalPemeriksaan = $pemeriksaan->count();
            $listPemeriksaan = $pemeriksaan->with('ditangani_oleh', 'pemilik_hewan', 'hewan.jenis_hewan', 'jenis_hewan')->orderBy('created_at', 'DESC')->limit(5)->get();

            Log::info('Berhasil mendapatkan data Dashboard!');
            return APIHelpers::responseAPI(
                [
                    'totalHewan' => $totalHewan,
                    'totalPemeriksaan' => $totalPemeriksaan,
                    'totalBlog' => $totalBlog,
                    'listPemeriksaan' => $listPemeriksaan
                ],
                200
            );
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Dashboard!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Dashboard!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function getUser()
    {
        try {
            $userID = Auth::guard('sanctum')->id();
            $user = User::with('user_detail')->where('id', $userID)->first();

            Log::info('Berhasil mendapatkan data User!');
            return APIHelpers::responseAPI(
                $user,
                200
            );
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data User!');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data User!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function changeAccount(Request $request)
    {
        try {
            $validate = $request->validate([
                'nama_depan' => 'required',
                'nama_belakang' => 'nullable',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore(Auth::guard('sanctum')->id()),
                ],
                'password' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'alamat' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:male,female',
                'mobile_code' => 'required|max:5',
                'mobile' => 'required',
            ]);

            $userID = Auth::guard('sanctum')->id();

            $dataUser = [
                'nama_depan' => $validate['nama_depan'],
                'nama_belakang' => $validate['nama_belakang'],
                'email' => $validate['email'],
            ];

            if ($validate['password']) {
                $dataUser['password'] = Hash::make($validate['password']);
            }

            if ($request->hasFile('avatar')) {
                $img = $validate['avatar']->store('user/avatar', 'public');
                $dataUser['avatar'] = 'storage/' . $img;
            }

            $dataDetailUser = [
                'alamat' => $validate['alamat'],
                'tempat_lahir' => $validate['tempat_lahir'],
                'tanggal_lahir' => $validate['tanggal_lahir'],
                'jenis_kelamin' => (string) $validate['jenis_kelamin'],
                'mobile' => $validate['mobile_code'] . $validate['mobile'],
            ];

            User::where('id', $userID)->update($dataUser);
            UserDetail::where('user_id', $userID)->update($dataDetailUser);
            $dataUser['id'] = $userID;

            Log::info('Successfully Edit Account');
            ActivityHelpers::LogActivityHelpers('Berhasil Merubah Account!', ['user' => $dataUser, 'detail' => $dataDetailUser], '1');
            return APIHelpers::responseAPI(['user' => $dataUser, 'detail' => $dataDetailUser], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Merubah Account!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
