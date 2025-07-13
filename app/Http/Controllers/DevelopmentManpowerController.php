<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DevelopmentManpower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DevelopmentManpowerController extends Controller
{
    /**
     * Dashboard dengan card kategori aktivitas
     */
     public function dashboard()
    {
        $categories = DevelopmentManpower::KATEGORI_AKTIVITAS;
        $counts = [];

        $user = Auth::user();

        foreach ($categories as $category) {
            if ($user->code_role == '001') {
                // Admin - show all data
                $counts[$category] = DevelopmentManpower::where('kategori_aktivitas', $category)->count();
            } else {
                // Regular user - show only their supervised data
                $counts[$category] = DevelopmentManpower::where('kategori_aktivitas', $category)
                    ->where('pengawas_id', $user->id)
                    ->count();
            }
        }

        return view('development_manpower.dashboard', compact('categories', 'counts'));
    }

    /**
     * Menampilkan semua data dalam tabel
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->code_role == '001') {
            // Admin - show all data
            $activities = DevelopmentManpower::with(['pengawas', 'pelakuKorban', 'saksi'])
                ->latest()
                ->paginate(10);
        } else {
            // Regular user - show only their supervised data
            $activities = DevelopmentManpower::with(['pengawas', 'pelakuKorban', 'saksi'])
                ->where('pengawas_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        return view('development_manpower.index', compact('activities'));
    }

    /**
     * Menampilkan form create berdasarkan kategori
     */
    public function create($kategori)
    {
        if (!in_array($kategori, DevelopmentManpower::KATEGORI_AKTIVITAS)) {
            abort(404);
        }

        $users = User::all();
        return view('development_manpower.create', compact('kategori', 'users'));
    }

    /**
     * Menyimpan data baru
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Handle file uploads
        $validated['foto_aktivitas'] = $this->uploadFile($request, 'foto_aktivitas');
        $validated['dokumen_1'] = $this->uploadFile($request, 'dokumen_1');
        $validated['dokumen_2'] = $this->uploadFile($request, 'dokumen_2');

        DevelopmentManpower::create($validated);

        return redirect()->route('development-manpower.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Menampilkan detail
     */
    public function show(DevelopmentManpower $development_manpower)
    {
        return view('development_manpower.show', compact('development_manpower'));
    }

    /**
     * Menampilkan form edit
     */
    public function edit(DevelopmentManpower $developmentManpower)
{
    $users = User::all();
    return view('development_manpower.edit', compact('developmentManpower', 'users'));
}

public function update(Request $request, DevelopmentManpower $developmentManpower)
{
    // Validasi data
    $validatedData = $request->validate([
        'tanggal_aktivitas' => 'required|date',
        'deskripsi' => 'required|string',
        'pengawas_id' => 'nullable|exists:data_users,id',
        'foto_aktivitas' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'dokumen_1' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        'dokumen_2' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
    ]);

    try {
        // Handle file uploads
        if ($request->hasFile('foto_aktivitas')) {
            // Hapus file lama jika ada
            if ($developmentManpower->foto_aktivitas) {
                Storage::delete('public/'.$developmentManpower->foto_aktivitas);
            }
            // Simpan file baru
            $validatedData['foto_aktivitas'] = $request->file('foto_aktivitas')->store('development_manpower', 'public');
        }

        if ($request->hasFile('dokumen_1')) {
            if ($developmentManpower->dokumen_1) {
                Storage::delete('public/'.$developmentManpower->dokumen_1);
            }
            $validatedData['dokumen_1'] = $request->file('dokumen_1')->store('development_manpower', 'public');
        }

        if ($request->hasFile('dokumen_2')) {
            if ($developmentManpower->dokumen_2) {
                Storage::delete('public/'.$developmentManpower->dokumen_2);
            }
            $validatedData['dokumen_2'] = $request->file('dokumen_2')->store('development_manpower', 'public');
        }

        $developmentManpower->update($validatedData);

        return redirect()->route('development-manpower.index')
            ->with('success', 'Data berhasil diperbarui');

    } catch (\Exception $e) {
        return back()->withInput()
            ->with('error', 'Gagal memperbarui data: '.$e->getMessage());
    }
}

private function validateRequest(Request $request, $model = null)
{
    $rules = [
        'kategori_aktivitas' => 'required|in:' . implode(',', DevelopmentManpower::KATEGORI_AKTIVITAS),
        'tanggal_aktivitas' => 'required|date',
        'deskripsi' => 'required|string|max:1000',
        'pengawas_id' => 'nullable|exists:data_users,id',
    ];

    // Aturan file untuk edit (nullable)
    $fileRules = [
        'foto_aktivitas' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'dokumen_1' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        'dokumen_2' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
    ];

    // Gabungkan aturan
    $rules = array_merge($rules, $fileRules);

    // Aturan khusus berdasarkan kategori
    switch ($request->kategori_aktivitas) {
        case 'SKKP/POP For GL Mitra':
        case 'Training HRCP Mitra':
            $rules['posisi'] = 'required|string|max:100';
            break;

        case 'Pembinaan Pelanggaran':
            $rules['pelaku_korban_id'] = 'required|exists:data_users,id';
            $rules['saksi_id'] = 'nullable|exists:data_users,id';
            $rules['kronologi'] = 'required|string|max:2000';
            break;
    }

    return $request->validate($rules);
}
    /**
     * Hapus data
     */
    public function destroy(DevelopmentManpower $development_manpower)
    {
        // Delete associated files
        $this->deleteFile($development_manpower->foto_aktivitas);
        $this->deleteFile($development_manpower->dokumen_1);
        $this->deleteFile($development_manpower->dokumen_2);

        $development_manpower->delete();

        return redirect()->route('development-manpower.index')
            ->with('success', 'Data berhasil dihapus');
    }


    /**
     * Upload file
     */
    private function uploadFile(Request $request, $fieldName)
    {
        if ($request->hasFile($fieldName)) {
            return $request->file($fieldName)->store('development_manpower', 'public');
        }
        return null;
    }

    /**
     * Delete file
     */
    private function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
