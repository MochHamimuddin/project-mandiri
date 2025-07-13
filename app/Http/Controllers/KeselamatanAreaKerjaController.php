<?php

namespace App\Http\Controllers;

use App\Models\KeselamatanAreaKerja;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KeselamatanAreaKerjaController extends Controller
{
    public function dashboard()
    {
        // Cek role user
        $user = auth()->user();

        $activities = [
            [
                'type' => 'inspeksi',
                'name' => 'Inspeksi & Observasi Tematik',
                'icon' => 'bi-clipboard2-check',
                'color' => 'primary',
                'count' => $user->code_role === '001' ?
                    KeselamatanAreaKerja::inspeksiObservasi()->count() :
                    KeselamatanAreaKerja::inspeksiObservasi()->where('created_by', $user->id)->count(),
                'route' => 'keselamatan.type.index',
                'report_route' => 'keselamatan.type.report',
                'create_route' => 'keselamatan.type.create'
            ],
            [
                'type' => 'gelar',
                'name' => 'Gelar/Inspeksi Tools',
                'icon' => 'bi-tools',
                'color' => 'success',
                'count' => $user->code_role === '001' ?
                    KeselamatanAreaKerja::gelarInspeksi()->count() :
                    KeselamatanAreaKerja::gelarInspeksi()->where('created_by', $user->id)->count(),
                'route' => 'keselamatan.type.index',
                'report_route' => 'keselamatan.type.report',
                'create_route' => 'keselamatan.type.create'
            ],
            [
                'type' => 'housekeeping',
                'name' => 'Housekeeping Workshop',
                'icon' => 'bi-house-gear',
                'color' => 'warning',
                'count' => $user->code_role === '001' ?
                    KeselamatanAreaKerja::housekeeping()->count() :
                    KeselamatanAreaKerja::housekeeping()->where('created_by', $user->id)->count(),
                'route' => 'keselamatan.type.index',
                'report_route' => 'keselamatan.type.report',
                'create_route' => 'keselamatan.type.create'
            ]
        ];

        return view('keselamatan.dashboard', compact('activities'));
    }

    public function index($type)
    {
        $title = KeselamatanAreaKerja::getActivityTypes()[$type] ?? 'Aktivitas';
        $user = auth()->user();

        // Query berdasarkan role
        $query = KeselamatanAreaKerja::with(['pengawas', 'mitra'])
            ->where('activity_type', $type);

        if ($user->code_role !== '001') {
            $query->where('created_by', $user->id);
        }

        $activities = $query->latest()->paginate(10);

        return view('keselamatan.index', compact('activities', 'title', 'type'));
    }

    public function create($type)
    {
        $title = 'Tambah ' . (KeselamatanAreaKerja::getActivityTypes()[$type] ?? 'Aktivitas');

        $pengawas = User::select('id', 'nama_lengkap', 'username')
                      ->orderBy('nama_lengkap')
                      ->get();

        $mitras = Mitra::select('id', 'nama_perusahaan')->get();

        return view('keselamatan.create', compact('title', 'type', 'pengawas', 'mitras'));
    }

    public function store(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'pengawas_id' => 'required|exists:data_users,id',
            'mitra_id' => 'required|exists:data_mitra,id',
            'deskripsi' => 'required|string|max:255',
            'path_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'path_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['pengawas_id', 'mitra_id', 'deskripsi']);
            $data['activity_type'] = $type;
            $data['is_approved'] = 0;
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            if ($request->hasFile('path_foto')) {
                $data['path_foto'] = $request->file('path_foto')->store('public/keselamatan/foto');
                $data['path_foto'] = str_replace('public/', '', $data['path_foto']);
            }

            if ($request->hasFile('path_file')) {
                $data['path_file'] = $request->file('path_file')->store('public/keselamatan/dokumen');
                $data['path_file'] = str_replace('public/', '', $data['path_file']);
            }

            KeselamatanAreaKerja::create($data);

            return redirect()->route('keselamatan.type.index', $type)
                ->with('success', 'Aktivitas berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $activity = KeselamatanAreaKerja::with(['pengawas', 'mitra', 'creator', 'updater'])->findOrFail($id);
        $user = auth()->user();

        $activityTypes = KeselamatanAreaKerja::getActivityTypes();

        return view('keselamatan.show', compact('activity', 'activityTypes'));
    }

    public function edit($id)
    {
        $activity = KeselamatanAreaKerja::findOrFail($id);
        $user = auth()->user();

        // Cek otorisasi
        if ($user->code_role !== '001' && $activity->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $pengawas = User::select('id', 'nama_lengkap')->get();
        $mitras = Mitra::select('id', 'nama_perusahaan')->get();

        return view('keselamatan.edit', compact('activity', 'pengawas', 'mitras'));
    }

    public function update(Request $request, $id)
    {
        $activity = KeselamatanAreaKerja::findOrFail($id);
        $user = auth()->user();

        // Cek otorisasi
        if ($user->code_role !== '001' && $activity->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'pengawas_id' => 'required|exists:data_users,id',
            'mitra_id' => 'required|exists:data_mitra,id',
            'deskripsi' => 'required|string|max:255',
            'path_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'path_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_approved' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['pengawas_id', 'mitra_id', 'deskripsi', 'is_approved']);
            $data['updated_by'] = auth()->id();

            if ($request->hasFile('path_foto')) {
                if ($activity->path_foto) {
                    Storage::delete($activity->path_foto);
                }
                $data['path_foto'] = $request->file('path_foto')->store('keselamatan/foto');
            }

            if ($request->hasFile('path_file')) {
                if ($activity->path_file) {
                    Storage::delete($activity->path_file);
                }
                $data['path_file'] = $request->file('path_file')->store('keselamatan/dokumen');
            }

            $activity->update($data);

            return redirect()->route('keselamatan.type.index', $activity->activity_type)
                ->with('success', 'Aktivitas berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $activity = KeselamatanAreaKerja::findOrFail($id);
        $user = auth()->user();

        // Cek otorisasi
        if ($user->code_role !== '001' && $activity->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if ($activity->path_foto) {
                Storage::delete($activity->path_foto);
            }
            if ($activity->path_file) {
                Storage::delete($activity->path_file);
            }

            $type = $activity->activity_type;
            $activity->delete();

            return redirect()->route('keselamatan.type.index', $type)
                ->with('success', 'Aktivitas berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function report($type)
    {
        $user = auth()->user();

        $typeMapping = [
            'inspeksi' => KeselamatanAreaKerja::TYPE_INSPEKSI_OBSERVASI,
            'gelar' => KeselamatanAreaKerja::TYPE_GELAR_INSPEKSI,
            'housekeeping' => KeselamatanAreaKerja::TYPE_HOUSEKEEPING
        ];

        $activityType = $typeMapping[$type] ?? $type;
        $title = 'Laporan ' . (KeselamatanAreaKerja::getActivityTypes()[$activityType] ?? 'Aktivitas');

        // Query berdasarkan role
        $query = KeselamatanAreaKerja::with(['pengawas', 'mitra'])
            ->where('activity_type', $activityType);

        if ($user->code_role !== '001') {
            $query->where('created_by', $user->id);
        }

        $activities = $query->latest()->get();

        return view('keselamatan.report', compact('activities', 'title', 'type'));
    }

    public function toggleApproval($id)
    {
        $activity = KeselamatanAreaKerja::findOrFail($id);
        $user = auth()->user();

        // Hanya admin yang bisa mengubah status approval
        if ($user->code_role !== '001') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini'
            ], 403);
        }

        try {
            $activity->update([
                'is_approved' => !$activity->is_approved,
                'updated_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'is_approved' => $activity->is_approved
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status persetujuan'
            ], 500);
        }
    }
}
