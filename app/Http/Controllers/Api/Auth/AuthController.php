<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request) {
        try {
            $validate = $request->validate([
                'username' => 'required|max:20',
                'password' => 'required'
            ]);

            if(Auth::attempt([
                'username' => $validate['username'],
                'password' => $validate['password']
            ])) {
                $user = User::where('username', $validate['username'])->first();
                if ($user->is_active) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    Log::info('Login Success');
                    ActivityHelpers::LogActivityHelpers('Login Berhasil!', [
                        'username' => $user->username,
                        'nama_depan' => $user->nama_depan,
                        'nama_belakang' => $user->nama_belakang,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'token' => $token,
                    ], '1');

                    return APIHelpers::responseAPI(['data' => [
                        'username' => $user->username,
                        'nama_depan' => $user->nama_depan,
                        'nama_belakang' => $user->nama_belakang,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'token' => $token,
                    ]], 200);
                } else {
                    Log::error('Akun belum aktif, silahkan melengkapi identitas!');
                    ActivityHelpers::LogActivityHelpers('Gagal Login! Akun Belum Terdaftar', [
                        'message' => 'Akun belum aktif, silahkan melengkapi identitas!'
                    ], '0');
                    return APIHelpers::responseAPI(['message' => 'Akun belum aktif, silahkan melengkapi identitas!'], 401);
                }
            }
        } catch (Exception $error) {
            Log::error($error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal Login!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });

            Log::info('Logout Success');
            ActivityHelpers::LogActivityHelpers('Logout Berhasil!', [
                'username' => $user->username,
                'message' => 'Logout Berhasil!',
            ], '1');

            return APIHelpers::responseAPI([
                'username' => $user->username,
                'message' => 'Logout Berhasil!',
            ], 200);
        } catch (\Exception $error) {

            Log::info('Logout Gagal!');
            ActivityHelpers::LogActivityHelpers('Logout Gagal!', [
                'message' => $error->getMessage(),
            ], '0');

            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
