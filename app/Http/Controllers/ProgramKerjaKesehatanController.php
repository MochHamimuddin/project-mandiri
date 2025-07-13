<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerjaKesehatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramKerjaKesehatanController extends Controller
{
    /**
     * Display dashboard with cards for each activity type
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Count data based on user role
        if ($user->code_role == '001') {
            // Admin - show all data
            $mcuCount = ProgramKerjaKesehatan::where('jenis_program', ProgramKerjaKesehatan::MCU_TAHUNAN)->count();
            $kronisCount = ProgramKerjaKesehatan::where('jenis_program', ProgramKerjaKesehatan::PENYAKIT_KRONIS)->count();

            $latestActivities = ProgramKerjaKesehatan::with(['pengawas', 'creator'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Regular user - show only their supervised data
            $mcuCount = ProgramKerjaKesehatan::where('jenis_program', ProgramKerjaKesehatan::MCU_TAHUNAN)
                ->where('pengawas_id', $user->id)
                ->count();

            $kronisCount = ProgramKerjaKesehatan::where('jenis_program', ProgramKerjaKesehatan::PENYAKIT_KRONIS)
                ->where('pengawas_id', $user->id)
                ->count();

            $latestActivities = ProgramKerjaKesehatan::with(['pengawas', 'creator'])
                ->where('pengawas_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('program-kesehatan.dashboard', [
            'mcuCount' => $mcuCount,
            'kronisCount' => $kronisCount,
            'latestActivities' => $latestActivities
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ProgramKerjaKesehatan::query()->with(['pengawas', 'creator']);

        // Filter by user role
        if ($user->code_role != '001') {
            $query->where('pengawas_id', $user->id);
        }

        // Filter by program type if selected
        if ($request->has('jenis_program')) {
            $query->where('jenis_program', $request->jenis_program);
        }

        $programs = $query->latest()->paginate(10);

        return view('program-kesehatan.index', [
            'programs' => $programs,
            'jenisProgram' => ProgramKerjaKesehatan::getJenisProgramOptions()
        ]);
    }


    /**
     * Show the form for creating a new resource
     */
    public function create(Request $request)
{
    $type = $request->query('type');

    // Perbaikan query pengawas:
    $pengawasList = User::whereHas('role', function($q) {
        $q->whereIn('code_role', ['001', '002']);
    })->get();

    // Debugging - Hapus setelah fix
    // dd($pengawasList); // Cek apakah data ada

    return view('program-kesehatan.create', [
        'type' => $type,
        'pengawasList' => $pengawasList
    ]);
}

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_program' => 'required|in:MCU_TAHUNAN,PENYAKIT_KRONIS',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'dokumen' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'deskripsi' => 'required|string|max:1000',
            'pengawas_id' => 'required|exists:data_users,id'
        ]);

        // Upload files
        $fotoPath = $request->file('foto')->store('program-kesehatan/fotos', 'public');
        $dokumenPath = $request->file('dokumen')->store('program-kesehatan/documents', 'public');

        ProgramKerjaKesehatan::create([
            'jenis_program' => $request->jenis_program,
            'foto_path' => $fotoPath,
            'dokumen_path' => $dokumenPath,
            'deskripsi' => $request->deskripsi,
            'pengawas_id' => $request->pengawas_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('program-kesehatan.index', ['type' => $request->jenis_program])
            ->with('success', 'Data program kesehatan berhasil ditambahkan');
    }

    /**
     * Display the specified resource
     */
    public function show(ProgramKerjaKesehatan $programKerjaKesehatan)
    {
        return view('program-kesehatan.show', [
            'program' => $programKerjaKesehatan->load('pengawas', 'creator')
        ]);
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(ProgramKerjaKesehatan $programKerjaKesehatan)
    {
        $pengawasList = User::whereHas('role', function($q) {
            $q->whereIn('code_role', ['001', '002']);
        })->get();

        return view('program-kesehatan.edit', [
            'program' => $programKerjaKesehatan,
            'pengawasList' => $pengawasList
        ]);
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, ProgramKerjaKesehatan $programKerjaKesehatan)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:1000',
            'pengawas_id' => 'required|exists:data_users,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
        ]);

        $data = [
            'deskripsi' => $request->deskripsi,
            'pengawas_id' => $request->pengawas_id,
            'updated_by' => Auth::id()
        ];

        // Handle foto update
        if ($request->hasFile('foto')) {
            // Delete old foto
            Storage::disk('public')->delete($programKerjaKesehatan->foto_path);

            // Store new foto
            $data['foto_path'] = $request->file('foto')->store('program-kesehatan/fotos', 'public');
        }

        // Handle dokumen update
        if ($request->hasFile('dokumen')) {
            // Delete old dokumen
            Storage::disk('public')->delete($programKerjaKesehatan->dokumen_path);

            // Store new dokumen
            $data['dokumen_path'] = $request->file('dokumen')->store('program-kesehatan/documents', 'public');
        }

        $programKerjaKesehatan->update($data);

        return redirect()->route('program-kesehatan.index', ['type' => $programKerjaKesehatan->jenis_program])
            ->with('success', 'Data program kesehatan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(ProgramKerjaKesehatan $programKerjaKesehatan)
    {
        // Delete files
        Storage::disk('public')->delete([
            $programKerjaKesehatan->foto_path,
            $programKerjaKesehatan->dokumen_path
        ]);

        $programKerjaKesehatan->update([
            'deleted_by' => Auth::id()
        ]);

        $programKerjaKesehatan->delete();

        return redirect()->route('program-kesehatan.index')
            ->with('success', 'Data program kesehatan berhasil dihapus');
    }

    /**
     * Download file attachment
     */
    public function downloadFile(ProgramKerjaKesehatan $program, $type)
    {
        $filePath = $type === 'foto' ? $program->foto_path : $program->dokumen_path;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('public')->download($filePath);
    }
}
