<?php

namespace App\Http\Controllers;

use App\Models\ProgramLingkunganHidup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramLingkunganHidupController extends Controller
{
    /**
     * Dashboard dengan card kategori aktivitas
     */
    public function dashboard()
    {
        $kridaCount = ProgramLingkunganHidup::kridaArea()->count();
        $pengelolaanCount = ProgramLingkunganHidup::pengelolaanLingkungan()->count();
        $latestActivities = ProgramLingkunganHidup::latest()->take(5)->get();

        return view('program-lingkungan.dashboard', [
            'kridaCount' => $kridaCount,
            'pengelolaanCount' => $pengelolaanCount,
            'latestActivities' => $latestActivities
        ]);
    }

    /**
     * Menampilkan tabel data
     */
    public function index()
    {
        $activities = ProgramLingkunganHidup::latest()->paginate(10);

        return view('program-lingkungan.index', compact('activities'));
    }

    /**
     * Menampilkan form create berdasarkan jenis kegiatan
     */
    public function create($jenis)
    {
        if (!in_array($jenis, ['krida', 'pengelolaan'])) {
            abort(404);
        }

        return view('program-lingkungan.create', ['jenis' => $jenis]);
    }

    /**
     * Menyimpan data baru
     */
    public function store(Request $request)
    {
        $jenis = $request->jenis_kegiatan;

        $rules = [
            'tanggal_kegiatan' => 'required|date',
            'lokasi' => 'required|string|max:100',
            'pelaksana' => 'required|string|max:100',
            'upload_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];

        if ($jenis === 'Krida Area Office/Workshop') {
            $rules['deskripsi'] = 'required|string';
        } else {
            $rules['detail_temuan'] = 'required|string';
            $rules['tindakan_perbaikan'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Upload foto jika ada
        if ($request->hasFile('upload_foto')) {
            $validated['upload_foto'] = $request->file('upload_foto')->store('lingkungan-hidup', 'public');
        }

        $validated['created_by'] = auth()->id();
        $validated['jenis_kegiatan'] = $jenis;

        ProgramLingkunganHidup::create($validated);

        return redirect()->route('program-lingkungan.index')
            ->with('success', 'Data kegiatan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail
     */
    public function show($id)
{
    $programLingkunganHidup = ProgramLingkunganHidup::findOrFail($id);

    return view('program-lingkungan.show', [
        'programLingkunganHidup' => $programLingkunganHidup
    ]);
}

    /**
     * Menampilkan form edit
     */
    public function edit(ProgramLingkunganHidup $programLingkunganHidup)
    {
        return view('program-lingkungan.edit', [
            'activity' => $programLingkunganHidup,
            'jenis' => strtolower(str_replace(' ', '-', explode('/', $programLingkunganHidup->jenis_kegiatan)[0]))
        ]);
    }

    /**
     * Update data
     */

    public function update(Request $request, ProgramLingkunganHidup $programLingkunganHidup)
{
    $jenis = $programLingkunganHidup->jenis_kegiatan;

    // Basic validation rules
    $rules = [
        'tanggal_kegiatan' => 'sometimes|required|date',
        'lokasi' => 'sometimes|required|string|max:100',
        'pelaksana' => 'sometimes|required|string|max:100',
        'upload_foto' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
        'deskripsi' => 'sometimes|nullable|string',
        'detail_temuan' => 'sometimes|nullable|string',
        'tindakan_perbaikan' => 'sometimes|nullable|string'
    ];

    // Validate only the fields present in request
    $validated = $request->validate($rules);

    // Initialize update data with original values
    $updateData = [
        'updated_by' => auth()->id()
    ];

    // Handle text fields - only update if present in request
    foreach ([
        'tanggal_kegiatan',
        'lokasi',
        'pelaksana',
        'deskripsi',
        'detail_temuan',
        'tindakan_perbaikan'
    ] as $field) {
        if ($request->has($field)) {
            $updateData[$field] = $request->input($field);
        } else {
            $updateData[$field] = $programLingkunganHidup->$field;
        }
    }

    // Handle file upload
    if ($request->has('hapus_foto') && $request->hapus_foto) {
        // Delete current photo if exists
        if ($programLingkunganHidup->upload_foto) {
            Storage::disk('public')->delete($programLingkunganHidup->upload_foto);
        }
        $updateData['upload_foto'] = null;
    }
    elseif ($request->hasFile('upload_foto')) {
        // Upload new photo
        if ($programLingkunganHidup->upload_foto) {
            Storage::disk('public')->delete($programLingkunganHidup->upload_foto);
        }
        $updateData['upload_foto'] = $request->file('upload_foto')->store('lingkungan-hidup', 'public');
    }
    else {
        // Keep existing photo if no changes
        $updateData['upload_foto'] = $programLingkunganHidup->upload_foto;
    }

    // Perform the update
    $programLingkunganHidup->update($updateData);

    return redirect()->route('program-lingkungan.index')
        ->with('success', 'Data kegiatan berhasil diperbarui');
}

    /**
     * Hapus data (soft delete)
     */
    public function destroy(ProgramLingkunganHidup $programLingkunganHidup)
    {
        $programLingkunganHidup->update([
            'deleted_by' => auth()->id()
        ]);

        $programLingkunganHidup->delete();

        return redirect()->route('program-lingkungan.index')
            ->with('success', 'Data kegiatan berhasil dihapus');
    }

    /**
     * API untuk mendapatkan data berdasarkan jenis (optional)
     */
    public function getByJenis($jenis)
    {
        $data = [];

        if ($jenis === 'krida') {
            $data = ProgramLingkunganHidup::kridaArea()->get();
        } elseif ($jenis === 'pengelolaan') {
            $data = ProgramLingkunganHidup::pengelolaanLingkungan()->get();
        }

        return response()->json($data);
    }
}
