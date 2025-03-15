<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\User;
use App\Models\UserDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function registerAccount(Request $request) 
    {
        try {
            $validate = $request->validate([
                'username' => 'unique|required|string|min:5|max:20',
                'nama_depan' => 'required',
                'nama_belakang' => 'required',
                'email' => 'unique|email',
                'password' => 'required|string',
                'avatar' => 'nullable|image:jpeg,png,jpg|max:2048'
            ]);

            $data = [
                'username' => $validate['username'],
                'nama_depan' => $validate['nama_depan'],
                'nama_belakang' => $validate['nama_belakang'],
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
                'is_active' => '0'
            ];

            if ($request->hasFile('avatar')) {
                $img = $request->file('img')->store('public/user/avatar/');
                $data['avatar'] = $img;
            }

            User::create($data);
            ActivityHelpers::LogActivityHelpers('Berhasil Membuat Account!', $request->all(), '0');
            Log::info('Successfully Create Account');
            return APIHelpers::responseAPI(['message' => 'Berhasil Membuat Account Silahkan Mengisi Data Detail', 'data' => $data], 200);
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Membuat Account!', ['message' => $error->getMessage(), 'data' => $data], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }

    }

    public function accountDetail($username, Request $request) 
    {
        try {
            $validate = $request->validate([
                'alamat' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:male,female',
                'phone' => 'nullable',
                'mobile' => 'nullable',
                'social_media' => 'nullable|string',
            ]);

            $user = User::where('username', $username)->first();
            $userID = $user->id;

            $user->update([
                'is_active' => '1'
            ]);

            UserDetail::updateOrCreate(['user_id' => $userID], [
                'user_id' => $userID,
                'alamat' => $validate['alamat'],
                'tempat_lahir' => $validate['tempat_lahir'],
                'tanggal_lahir' => $validate['tanggal_lahir'],
                'jenis_kelamin' => (string) $validate['jenis_kelamin'],
                'phone' => $validate['phone'],
                'mobile' => $validate['mobile'],
                'social_media' => $validate['social_media'],
            ]);

        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Melengkapkan Biodata Account!', [
                'message' => $error->getMessage(),
                'data' => [
                    'user_id' => $userID,
                    'alamat' => $validate['alamat'],
                    'tempat_lahir' => $validate['tempat_lahir'],
                    'tanggal_lahir' => $validate['tanggal_lahir'],
                    'jenis_kelamin' => (string) $validate['jenis_kelamin'],
                    'phone' => $validate['phone'],
                    'mobile' => $validate['mobile'],
                    'social_media' => json_encode($validate['social_media']),
                ]
            ], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
