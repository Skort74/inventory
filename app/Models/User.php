<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'branch_id', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    // LENGKAPI DI SINI: Tambahkan HasApiTokens ke dalam daftar trait class
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kebutuhan JWT: Ambil ID User untuk dimasukkan ke sub-claim JWT
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'https://hasura.io/jwt/claims' => [
                'x-hasura-default-role' => 'user',
                'x-hasura-allowed-roles' => ['user'],
                'x-hasura-user-id' => (string)$this->id, // Mengambil ID dari user yang sedang login
            ],
        ];
        $token = auth()->claims($customClaims)->login($user);

    return response()->json(compact('token'));
    }
    
}