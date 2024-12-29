<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helper\APIHelpers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if(!$token = auth()->guard('api')->attempt($credentials)) {
            APIHelpers::responseAPI(['message' => 'Email atau Password Anda salah'], 401);
        }

        APIHelpers::responseAPI([
            'user' => auth()->guard('api')->user(),    
            'token'   => $token
        ], 200);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        if($user) {
            APIHelpers::responseAPI([
                'user'    => $user,
            ], 200);
        }

        APIHelpers::responseAPI([
            'message' => 'Gagal membuat akun!'
        ], 409);
    }
}
