<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    // 1. Daftarkan kolom yang boleh diisi (Mass Assignment)
    // Ini wajib diisi agar Seeder yang kita buat tadi tidak error
    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'alamat',
    ];

    /**
     * 2. Relasi: Satu cabang mempunyai banyak User/Staff (One-to-Many)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}