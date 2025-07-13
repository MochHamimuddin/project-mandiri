<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FirePreventiveManagement;

class FirePreventiveController extends Controller
{
    // 1. Dashboard Card
    public function dashboard()
    {
        $pencucianCount = FirePreventiveManagement::where('activity_type', 'Pencucian Unit')->count();
        $inspeksiCount = FirePreventiveManagement::where('activity_type', 'Inspeksi APAR')->count();

        return view('fire-preventive.dashboard', [
            'pencucianCount' => $pencucianCount,
            'inspeksiCount' => $inspeksiCount
        ]);
    }

    // 2. Index dengan Tabel
    public function index()
    {
        $data = FirePreventiveManagement::with(['supervisor', 'creator'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('fire-preventive.index', [
            'activities' => $data
        ]);
    }

    // 3. Create Form
    public function create($type)
    {
        try {
            // Validasi tipe aktivitas
            if (!in_array($type, ['Pencucian Unit', 'Inspeksi APAR'])) {
                abort(404, 'Jenis aktivitas tidak valid');
            }

            // Ambil data pengawas
            $supervisors = User::whereHas('role', function($q) {
                $q->whereIn('code_role', ['001', '002']);
            })->orderBy('nama_lengkap')->get();

            // Fallback jika tidak ada hasil dari whereHas
            if ($supervisors->isEmpty()) {
                $supervisors = User::whereIn('code_role', ['001', '002'])
                                ->orderBy('nama_lengkap')
                                ->get();
            }

            return view('fire-preventive.create', [
                'type' => $type,
                'supervisors' => $supervisors
            ]);

        } catch (\Exception $e) {
            return redirect()->route('fire-preventive.index')
                           ->with('error', 'Gagal memuat form: '.$e->getMessage());
        }
    }

    // Store Data
    public function store(Request $request)
    {
        try {
            // Validasi tipe aktivitas
            $validTypes = ['Pencucian Unit', 'Inspeksi APAR'];
            if (!in_array($request->activity_type, $validTypes)) {
                throw new \Exception('Jenis aktivitas tidak valid');
            }

            $request->validate($this->validationRules($request->activity_type));

            $data = $request->except('foto', 'form_fpp');
            $data['created_by'] = Auth::id();

            // Handle file upload foto
            if ($request->hasFile('foto')) {
                $data['foto_path'] = $this->uploadFile($request->file('foto'), 'fire-preventive/fotos');
            }

            // Handle file upload form FPP khusus pencucian
            if ($request->activity_type === 'Pencucian Unit' && $request->hasFile('form_fpp')) {
                $data['form_fpp_path'] = $this->uploadFile($request->file('form_fpp'), 'fire-preventive/forms');
            }

            // Buat record baru
            FirePreventiveManagement::create($data);

            return redirect()->route('fire-preventive.index')
                           ->with('success', 'Data berhasil disimpan');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    // Helper method untuk upload file
    private function uploadFile($file, $directory)
    {
        try {
            return $file->store($directory, 'public');
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengunggah file: '.$e->getMessage());
        }
    }

    // Show Detail
    public function show($id)
    {
        $activity = FirePreventiveManagement::with(['supervisor', 'creator'])->findOrFail($id);

        return view('fire-preventive.show', compact('activity'));
    }

    // Edit Form
    public function edit($id)
{
    $activity = FirePreventiveManagement::with(['supervisor', 'creator'])->findOrFail($id);

    // Versi 1: Jika menggunakan code_role
    $supervisors = User::whereHas('role', function($q) {
        $q->whereIn('code_role', ['001', '002']); // Sesuaikan dengan kode role pengawas
    })->orderBy('nama_lengkap')->get();

    if ($supervisors->isEmpty()) {
        $supervisors = User::whereIn('code_role', ['001', '002'])->get();
    }

    return view('fire-preventive.edit', [
        'activity' => $activity,
        'supervisors' => $supervisors
    ]);
}

    // Update Data
    public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $activity = FirePreventiveManagement::findOrFail($id);

        $request->validate($this->validationRules($activity->activity_type));

        // Ambil semua data kecuali token dan file
        $data = $request->except(['_token', '_method', 'foto', 'form_fpp']);
        $data['updated_by'] = auth()->id();

        // Handle foto update
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($activity->foto_path) {
                Storage::disk('public')->delete($activity->foto_path);
            }
            $data['foto_path'] = $request->file('foto')->store('fire-preventive/fotos', 'public');
        } else {
            // Pertahankan value lama jika tidak ada input baru
            $data['foto_path'] = $activity->foto_path;
        }

        // Handle form FPP khusus untuk Pencucian Unit
        if ($activity->activity_type === 'Pencucian Unit') {
            if ($request->hasFile('form_fpp')) {
                // Delete old form if exists
                if ($activity->form_fpp_path) {
                    Storage::disk('public')->delete($activity->form_fpp_path);
                }
                $data['form_fpp_path'] = $request->file('form_fpp')->store('fire-preventive/forms', 'public');
            } else {
                // Pertahankan value lama jika tidak ada input baru
                $data['form_fpp_path'] = $activity->form_fpp_path;
            }
        }

        $activity->update($data);
        DB::commit();

        return redirect()->route('fire-preventive.index')
                       ->with('success', 'Data berhasil diperbarui');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
                       ->withInput()
                       ->with('error', 'Gagal memperbarui data: '.$e->getMessage());
    }
}

private function validationRules($type)
{
    $rules = [
        'supervisor_id' => 'required|exists:data_users,id',
        'description' => 'required|string',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ];

    if ($type === 'Pencucian Unit') {
        $rules['form_fpp'] = 'nullable|file|mimes:pdf,doc,docx|max:5120';
    } else {
        $rules['inspection_location'] = 'required|string|max:100';
    }

    return $rules;
}

    // Delete Data
    public function destroy($id)
    {
        $activity = FirePreventiveManagement::findOrFail($id);
        $activity->deleted_by = Auth::id();
        $activity->save();

        $activity->delete();

        return redirect()->route('fire-preventive.index')
                         ->with('success', 'Data berhasil dihapus');
    }
}
