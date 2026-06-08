<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        // 1. Mencoba login dan mendapatkan user
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah, bos!'
            ], 401);
        }

        // 2. Ambil instance user yang sedang login
        $user = Auth::guard('api')->user();

        // 3. Generate token baru yang menyertakan custom claims untuk Hasura
        // JWTAuth::claims() akan mengambil data dari method getJWTCustomClaims() di User Model
        $token = Auth::guard('api')->claims($user->getJWTCustomClaims())->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'token'   => $token,
            'token_type' => 'bearer'
        ], 200);
    }

    public function me()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diambil',
            'data'    => Auth::guard('api')->user()
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout!'
        ], 200);
    }
}