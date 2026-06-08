<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use GraphQL\Error\Error;

class Login
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // 1. Cari user berdasarkan email
        $user = User::where('email', $args['email'])->first();

        // 2. Validasi user & password
        if (!$user || !Hash::check($args['password'], $user->password)) {
            throw new Error('Email atau password kamu salah, bos!');
        }

        // 3. GENERATE TOKEN (PILIH SALAH SATU OPSI DI BAWAH)

        // --- OPSI A: Kalau kamu pakai Laravel Sanctum ---
        $token = $user->createToken('auth_token')->plainTextToken;

        // --- OPSI B: Kalau kamu pakai Tymon JWT (jwt.io) ---
        // $token = auth()->login($user); 
        // if (!$token) throw new Error('Gagal generate JWT Token');


        // 4. Kembalikan data sesuai struktur AuthPayload di schema.graphql
        return [
            'token' => $token,
            'user' => $user
        ];
    }
}