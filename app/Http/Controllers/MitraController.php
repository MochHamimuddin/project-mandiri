<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{

    public function index()
{
    $mitras = Mitra::with(['picUser'])
                 ->orderBy('id', 'desc')
                 ->get();
    return view('data_mitra.index', compact('mitras'));
}

    public function create()
    {
        $users = User::select('id', 'nama_lengkap')
                    ->orderBy('nama_lengkap')
                    ->get();

        return view('data_mitra.create', compact('users'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_perusahaan' => 'required|string|max:255',
        'alamat' => 'required|string',
        'pic' => 'required|integer|exists:data_users,id',
    ]);

    DB::beginTransaction();

    try {
        $mitra = Mitra::create([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'alamat' => $validated['alamat'],
            'pic' => (int)$validated['pic'],
            'created_by' => Auth::user()->nama_lengkap, // Simpan nama user langsung
        ]);

        DB::commit();

        return redirect()->route('mitra.index')
                       ->with('success', 'Mitra berhasil ditambahkan');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
                   ->with('error', 'Gagal menambahkan mitra: '.$e->getMessage());
    }
}

    public function edit($id)
    {
        $mitra = Mitra::findOrFail($id);
        $users = User::select('id', 'nama_lengkap')
                    ->orderBy('nama_lengkap')
                    ->get();

        return view('data_mitra.edit', compact('mitra', 'users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'pic' => 'required|integer|exists:data_users,id',
        ]);

        DB::beginTransaction();

        try {
            $mitra = Mitra::findOrFail($id);
            $mitra->update([
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'alamat' => $validated['alamat'],
                'pic' => (int) $validated['pic'],
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('mitra.index')
                           ->with('success', 'Mitra berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Gagal memperbarui mitra: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $mitra = Mitra::findOrFail($id);
            $mitra->update([
                'deleted_by' => Auth::id(),
            ]);
            $mitra->delete();

            DB::commit();

            return redirect()->route('data_mitra.index')
                           ->with('success', 'Mitra berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus mitra: '.$e->getMessage());
        }
    }
}
