<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Masukkan data Branch dulu dan simpan ke dalam variabel $branch
        $branch = Branch::updateOrCreate(
            ['kode_cabang' => 'KTT001'],
            [
                // TIPS: Kalau di modul Tubes kamu mengharuskan ID Cabang wajib angka 5, 
                // kamu bisa hapus komentar baris di bawah ini:
                // 'id' => 5, 
                'nama_cabang' => 'Cabang Surabaya',
                'alamat'      => 'Jl. Ketintang No.156, Ketintang, Kec. Gayungan, Surabaya',
            ]
        );

        // 2. Buat user dengan mengambil ID branch secara dinamis
        User::updateOrCreate(
            ['email' => 'admin@mail.com'], 
            [
                'id'        => 101,
                'name'      => 'Muhammad Josfi',
                'password'  => Hash::make('password123'),
                'role'      => 'manager_cabang',
                'branch_id' => $branch->id, // <--- Mengikuti ID branch yang sukses dibuat di atas
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@cabang5.com'],
            [
                'id'        => 102,
                'name'      => 'Staff Cabang',
                'password'  => Hash::make('password123'),
                'role'      => 'staff', 
                'branch_id' => $branch->id, // <--- Mengikuti ID branch yang sukses dibuat di atas
            ]
        );
    }
}