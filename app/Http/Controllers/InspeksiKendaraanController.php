<?php

namespace App\Http\Controllers;

use App\Models\InspeksiKendaraan;
use App\Models\User;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InspeksiKendaraanController extends Controller
{
    /**
     * Display dashboard with activity cards
     */
    public function dashboard()
{
    try {
        // Data for Inspection & Thematic Observation
        $totalKomisioning = InspeksiKendaraan::where('jenis_inspeksi', 'komisioning')->count();
        $totalPerawatan = InspeksiKendaraan::where('jenis_inspeksi', 'perawatan')->count();

        // PERBAIKAN: Ganti 'user' dengan 'pengawas'
        $inspeksiTerbaru = InspeksiKendaraan::with(['pengawas', 'mitra'])
            ->whereIn('jenis_inspeksi', ['komisioning', 'perawatan'])
            ->latest()
            ->take(5)
            ->get();

        $jadwalPerawatanMendatang = InspeksiKendaraan::with(['pengawas', 'mitra'])
            ->where('jenis_inspeksi', 'perawatan')
            ->whereNotNull('jadwal_perawatan')
            ->where('jadwal_perawatan', '>=', now())
            ->orderBy('jadwal_perawatan')
            ->take(5)
            ->get();

        // Data for Speed Evaluation
        $totalEvaluasi = InspeksiKendaraan::where('jenis_inspeksi', 'evaluasi_kecepatan')->count();

        // PERBAIKAN: Ganti 'user' dengan 'pengawas'
        $evaluasiTerbaru = InspeksiKendaraan::with(['pengawas', 'mitra'])
            ->where('jenis_inspeksi', 'evaluasi_kecepatan')
            ->latest()
            ->take(5)
            ->get();

        return view('inspeksi.dashboard', compact(
            'totalKomisioning',
            'totalPerawatan',
            'inspeksiTerbaru',
            'jadwalPerawatanMendatang',
            'totalEvaluasi',
            'evaluasiTerbaru'
        ));
    } catch (\Exception $e) {
        Log::error('Dashboard error: ' . $e->getMessage());
        return view('inspeksi.dashboard', [
            'error' => $e->getMessage(),
            'totalKomisioning' => 0,
            'totalPerawatan' => 0,
            'totalEvaluasi' => 0,
            'inspeksiTerbaru' => [],
            'evaluasiTerbaru' => [],
            'jadwalPerawatanMendatang' => []
        ]);
    }
}

    /**
     * Display listing of inspections
     */
    public function index(Request $request)
    {
        $query = InspeksiKendaraan::with(['pengawas', 'mitra'])
            ->latest();

        // Filter by inspection type
        if ($request->has('jenis')) {
            $query->where('jenis_inspeksi', $request->jenis);
        }

        // Filter by date
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal_inspeksi', $request->tanggal);
        }

        $inspeksi = $query->paginate(10);

        return view('inspeksi.index', compact('inspeksi'));
    }

    /**
     * Show form to create new inspection
     */
    public function create()
    {
        $users = User::all();
        $mitras = Mitra::all();

        return view('inspeksi.create', compact('users', 'mitras'));
    }

    /**
     * Store a newly created inspection
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'jenis_inspeksi' => 'required|in:komisioning,perawatan,evaluasi_kecepatan',
        'tanggal_inspeksi' => 'required|date',
        'deskripsi' => 'required|string|max:255',
        'pengawas_id' => 'nullable|exists:data_users,id',
        'mitra_id' => 'nullable|exists:data_mitra,id',
        'jenis_komisioning' => 'required_if:jenis_inspeksi,komisioning|nullable|string|max:50',
        'jadwal_perawatan' => 'required_if:jenis_inspeksi,perawatan|nullable|date',
        'pelaksana_perawatan' => 'required_if:jenis_inspeksi,perawatan|nullable|string|max:100',
        'hasil_observasi_kecepatan' => 'required_if:jenis_inspeksi,evaluasi_kecepatan|nullable|string|max:20',
        'satuan_kecepatan' => 'required_if:jenis_inspeksi,evaluasi_kecepatan|nullable|string|max:20',
        'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'dokumen' => 'nullable|file|mimes:pdf|max:5120',
    ]);

    try {
        // Upload photo
        $fotoPath = $request->file('foto')->store('public/inspeksi/foto');
        $validated['path_foto'] = $fotoPath;

        // Upload document if exists
        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('public/inspeksi/dokumen');
            $validated['path_dokumen'] = $dokumenPath;
        }

        // Save data
        $inspeksi = InspeksiKendaraan::create($validated);

        return redirect()->route('inspeksi.index')
            ->with('success', 'Data inspeksi berhasil disimpan!');

    } catch (\Exception $e) {
        Log::error('Store inspection error: ' . $e->getMessage());
        return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}

    /**
     * Display specific inspection
     */
    public function show(InspeksiKendaraan $inspeksi)
    {
        return view('inspeksi.show', compact('inspeksi'));
    }

    /**
     * Show form to edit inspection
     */
    public function edit(InspeksiKendaraan $inspeksi)
    {
        $users = User::all();
        $mitras = Mitra::all();

        return view('inspeksi.edit', compact('inspeksi', 'users', 'mitras'));
    }

    /**
     * Update inspection data
     */
    public function update(Request $request, InspeksiKendaraan $inspeksi)
    {
        $validated = $request->validate([
            'jenis_inspeksi' => 'required|in:komisioning,perawatan,evaluasi_kecepatan',
            'tanggal_inspeksi' => 'required|date',
            'deskripsi' => 'required|string|max:255',
            'pengawas_id' => 'nullable|exists:data_users,id',
            'mitra_id' => 'nullable|exists:data_mitra,id',
            'jenis_komisioning' => 'required_if:jenis_inspeksi,komisioning|nullable|string|max:50',
            'jadwal_perawatan' => 'required_if:jenis_inspeksi,perawatan|nullable|date',
            'pelaksana_perawatan' => 'required_if:jenis_inspeksi,perawatan|nullable|string|max:100',
            'hasil_observasi_kecepatan' => 'required_if:jenis_inspeksi,evaluasi_kecepatan|nullable|string|max:20',
            'satuan_kecepatan' => 'required_if:jenis_inspeksi,evaluasi_kecepatan|nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dokumen' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        try {
            // Update photo if exists
            if ($request->hasFile('foto')) {
                // Delete old photo
                Storage::delete($inspeksi->path_foto);

                // Save new photo
                $fotoPath = $request->file('foto')->store('public/inspeksi/foto');
                $validated['path_foto'] = $fotoPath;
            }

            // Update document if exists
            if ($request->hasFile('dokumen')) {
                // Delete old document if exists
                if ($inspeksi->path_dokumen) {
                    Storage::delete($inspeksi->path_dokumen);
                }

                // Save new document
                $dokumenPath = $request->file('dokumen')->store('public/inspeksi/dokumen');
                $validated['path_dokumen'] = $dokumenPath;
            }

            $inspeksi->update($validated);

            return redirect()->route('inspeksi.index')
                ->with('success', 'Data inspeksi berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Update inspection error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Delete inspection
     */
    public function destroy(InspeksiKendaraan $inspeksi)
    {
        try {
            // Delete associated files
            Storage::delete($inspeksi->path_foto);
            if ($inspeksi->path_dokumen) {
                Storage::delete($inspeksi->path_dokumen);
            }

            $inspeksi->delete();

            return redirect()->route('inspeksi.index')
                ->with('success', 'Data inspeksi berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Delete inspection error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
