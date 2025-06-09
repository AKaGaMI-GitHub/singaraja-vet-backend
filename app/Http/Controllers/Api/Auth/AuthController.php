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
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validate = $request->validate([
                'username' => 'required|max:20',
                'password' => 'required'
            ]);

            if (Auth::attempt([
                'username' => $validate['username'],
                'password' => $validate['password']
            ])) {
                $user = $request->user();
                if ($user->is_active) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    Log::info('Login Success');
                    ActivityHelpers::LogActivityHelpers('Login Berhasil!', [
                        'id' => $user->id,
                        'username' => $user->username,
                        'nama_depan' => $user->nama_depan,
                        'nama_belakang' => $user->nama_belakang,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'is_vet' => (string) $user->is_vet,
                        'token' => $token,
                    ], '1');
                    return APIHelpers::responseAPI(['message' => 'Login Berhasil!', 'data' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'nama_depan' => $user->nama_depan,
                        'nama_belakang' => $user->nama_belakang,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'is_vet' => (string) $user->is_vet,
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
            $user = Auth::guard('sanctum')->user();
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

    // public function redirectToProvider($provider)
    // {
    //     return Socialite::driver($provider)->redirect();
    // }

    // public function handleGoogleCallback()
    // {
    //     try {
    //         $googleUser = Socialite::driver('google')->stateless()->user();
    //         $user = User::where('email', $googleUser->email)->first();
    //         if (!$user) {
    //             $user = User::create(['nama_depan' => $googleUser->name, 'email' => $googleUser->email, 'password' => Hash::make(rand(100000, 999999))]);
    //             ActivityHelpers::LogActivityHelpers('Berhasil Membuat Account (SSO Google)!', ['nama_depan' => $googleUser->name, 'email' => $googleUser->email, 'password' => Hash::make(rand(100000, 999999))], '1');
    //             Log::info('Successfully Create Account');
    //             return APIHelpers::responseAPI(['message' => 'Berhasil Membuat Account Silahkan Mengisi Data Detail', 'data' => ['nama_depan' => $googleUser->name, 'email' => $googleUser->email], 'redirect' => '/register?nama_depan=' . $googleUser->name . '&email=' . $googleUser->email], 200);
    //         }

    //         $token = $user->createToken('auth_token')->plainTextToken;
    //         Log::info('Login Success');
    //         ActivityHelpers::LogActivityHelpers('Login Berhasil (SSO Google) !', [
    //             'username' => $user->username,
    //             'nama_depan' => $user->nama_depan,
    //             'nama_belakang' => $user->nama_belakang,
    //             'email' => $user->email,
    //             'avatar' => $user->avatar,
    //             'token' => $token,
    //         ], '1');

    //         return APIHelpers::responseAPI(['message' => 'Login Berhasil!', 'data' => [
    //             'username' => $user->username,
    //             'nama_depan' => $user->nama_depan,
    //             'nama_belakang' => $user->nama_belakang,
    //             'email' => $user->email,
    //             'avatar' => $user->avatar,
    //             'token' => $token,
    //         ]], 200);
    //     } catch (Exception $error) {

    //         Log::info('Logout Google Gagal!');
    //         ActivityHelpers::LogActivityHelpers('Logout Google Gagal!', [
    //             'message' => $error->getMessage(),
    //         ], '0');

    //         return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
    //     }
    // }
}
