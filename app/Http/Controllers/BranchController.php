<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    // [READ ALL] - Mengambil semua data cabang
    public function index()
    {
        $branches = Branch::all();
        return response()->json([
            'status' => 'success',
            'data' => $branches
        ], 200);
    }

    // [CREATE] - Menambahkan cabang baru (Hanya Admin)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_cabang' => 'required|string|unique:branches,kode_cabang',
            'nama_cabang' => 'required|string|max:255',
            'alamat'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $branch = Branch::create($request->all());

        return response()->json([
            'message' => 'Cabang baru berhasil ditambahkan!',
            'data' => $branch
        ], 201);
    }

    // [READ SINGLE] - Mengambil satu data cabang berdasarkan ID
    public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Cabang tidak ditemukan!'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $branch
        ], 200);
    }

    // [UPDATE] - Mengubah data cabang (Hanya Admin)
    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Cabang tidak ditemukan!'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_cabang' => 'string|unique:branches,kode_cabang,' . $id,
            'nama_cabang' => 'string|max:255',
            'alamat'      => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $branch->update($request->all());

        return response()->json([
            'message' => 'Data cabang berhasil diperbarui!',
            'data' => $branch
        ], 200);
    }

    // [DELETE] - Menghapus data cabang (Hanya Admin)
    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Cabang tidak ditemukan!'], 404);
        }

        $branch->delete();

        return response()->json([
            'message' => 'Cabang berhasil dihapus dari sistem!'
        ], 200);
    }

    // Fungsi placeholder dummy kemarin tetap aman di bawah
    public function managerStaffMethod()
    {
        return response()->json(['message' => 'Sukses tembus middleware Manager/Staff!']);
    }

    public function adminMethod()
    {
        return response()->json(['message' => 'Sukses tembus middleware Admin!']);
    }
}