<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

// --- ROUTE PUBLIC ---
Route::post('/login', [AuthController::class, 'login']);

// --- ROUTE PROTECTED (Wajib Token JWT) ---
Route::middleware('auth:api')->group(function () {
    
    // Fitur Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Semua yang sudah login (Manager & Staff) bisa melihat data cabang
    Route::get('/branches', [BranchController::class, 'index']);      // Lihat semua
    Route::get('/branches/{id}', [BranchController::class, 'show']);  // Lihat satu data

    // --- PROTEKSI ROLE KHUSUS ---

    // GANTI DI SINI: Khusus Manager Cabang (Bisa Tambah, Edit, Hapus)
    Route::middleware('role:manager_cabang')->group(function () {
        Route::post('/branches', [BranchController::class, 'store']);       // Create
        Route::put('/branches/{id}', [BranchController::class, 'update']);   // Update
        Route::delete('/branches/{id}', [BranchController::class, 'destroy']); // Delete
    });

    // Khusus Staff (Hanya bisa akses method dummy kemarin, tidak bisa CRUD di atas)
    Route::middleware('role:staff')->group(function () {
        Route::get('/branch-data', [BranchController::class, 'managerStaffMethod']);
    });
});