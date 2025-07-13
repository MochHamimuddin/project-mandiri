<?php

namespace App\Http\Controllers;

use App\Models\DataSib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DataSibController extends Controller
{
    // Define the base storage path
    private const BASE_STORAGE_PATH = 'public/sib_files/';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // For role 001 (admin) show all data
        if (Auth::user()->code_role == '001') {
            $dataSibs = DataSib::with('creator')->latest()->paginate(10);
        } else {
            // For role 002 (user) only show their own data
            $dataSibs = DataSib::where('created_by', Auth::id())->latest()->paginate(10);
        }

        return view('data_sib.index', compact('dataSibs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data_sib.create', [
            'departemenOptions' => DataSib::DEPARTEMEN,
            'perihalOptions' => DataSib::PERIHAL,
            'lokasiOptions' => DataSib::LOKASI,
            'jenisPekerjaanOptions' => DataSib::JENIS_PEKERJAAN,
            'yaTidakOptions' => DataSib::YA_TIDAK,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(DataSib::rules(), DataSib::messages());

        // Prepare data without files
        $data = $request->except([
            '_token', 'work_permit', 'jsa', 'ibpr', 'emergency_preparedness',
            'emergency_escape_plan', 'staggling_plan', 'history_training',
            'kajian_geotek', 'form_fpp', 'form_observasi_berjenjang',
            'form_p2h_unit_lifting', 'form_inspeksi_tools'
        ]);

        // Set creator
        $data['created_by'] = Auth::id();

        // Handle file uploads
        $this->handleFileUploads($request, $data);

        // Create record
        DataSib::create($data);

        if (auth()->user()->code_role === '002') {
            return redirect()->route('daftar-laporan')
                            ->with('success', 'Data SIB berhasil dibuat.');
        }

        return redirect()->route('data-sib.index')
                        ->with('success', 'Data SIB berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataSib $dataSib)
    {
        // Authorization - only creator or admin can view
        $this->authorizeView($dataSib);

        return view('data_sib.show', ['sib' => $dataSib]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataSib $dataSib)
    {
        // Only admin can edit
        if (Auth::user()->code_role != '001') {
            abort(403, 'Unauthorized action.');
        }

        return view('data_sib.edit', [
            'sib' => $dataSib,
            'departemenOptions' => DataSib::DEPARTEMEN,
            'perihalOptions' => DataSib::PERIHAL,
            'lokasiOptions' => DataSib::LOKASI,
            'jenisPekerjaanOptions' => DataSib::JENIS_PEKERJAAN,
            'yaTidakOptions' => DataSib::YA_TIDAK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataSib $dataSib)
    {
        // Only admin can update
        if (Auth::user()->code_role != '001') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate(DataSib::rules($dataSib->id), DataSib::messages());

        // Prepare data without files
        $data = $request->except([
            '_token', '_method', 'work_permit', 'jsa', 'ibpr', 'emergency_preparedness',
            'emergency_escape_plan', 'staggling_plan', 'history_training',
            'kajian_geotek', 'form_fpp', 'form_observasi_berjenjang',
            'form_p2h_unit_lifting', 'form_inspeksi_tools'
        ]);

        // Handle file uploads
        $this->handleFileUploads($request, $data, $dataSib);

        $dataSib->update($data);

        return redirect()->route('data-sib.index')
                        ->with('success', 'Data SIB berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataSib $dataSib)
    {
        // Only admin can delete
        if (Auth::user()->code_role != '001') {
            abort(403, 'Unauthorized action.');
        }

        // Delete all associated files
        $this->deleteAllFiles($dataSib);

        $dataSib->delete();

        return redirect()->route('data-sib.index')
                        ->with('success', 'Data SIB berhasil dihapus.');
    }

    /**
     * Handle all file uploads for create and update
     */
    private function handleFileUploads(Request $request, array &$data, DataSib $dataSib = null)
{
    // Define all file fields and their storage paths
    $fileFields = [
        'work_permit' => ['path' => 'work_permits', 'db_field' => 'work_permit_path'],
        'emergency_preparedness' => ['path' => 'emergency_docs', 'db_field' => 'emergency_preparedness_path'],
        'emergency_escape_plan' => ['path' => 'emergency_docs', 'db_field' => 'emergency_escape_plan_path'],
        'staggling_plan' => ['path' => 'emergency_docs', 'db_field' => 'staggling_plan_path'],
        'history_training' => ['path' => 'training_docs', 'db_field' => 'history_training_path'],
        'kajian_geotek' => ['path' => 'work_permits', 'db_field' => 'kajian_geotek_path'],
        'form_fpp' => ['path' => 'forms', 'db_field' => 'form_fpp_path'],
        'form_observasi_berjenjang' => ['path' => 'forms', 'db_field' => 'form_observasi_berjenjang_path'],
        'form_p2h_unit_lifting' => ['path' => 'forms', 'db_field' => 'form_p2h_unit_lifting_path'],
        'form_inspeksi_tools' => ['path' => 'forms', 'db_field' => 'form_inspeksi_tools_path'],
    ];

    // Handle single file uploads
    foreach ($fileFields as $field => $config) {
        if ($request->hasFile($field)) {
            // Delete old file if updating
            if ($dataSib && $dataSib->{$config['db_field']}) {
                Storage::delete($dataSib->{$config['db_field']});
            }

            $storagePath = self::BASE_STORAGE_PATH . $config['path'];
            $uploadedFile = $request->file($field);

            // Generate unique filename with original extension
            $filename = $uploadedFile->hashName();
            $path = $uploadedFile->storeAs($storagePath, $filename);

            // Store relative path (without 'public/' prefix)
            $data[$config['db_field']] = str_replace('public/', '', $path);
        }
    }

    // Handle JSA files (max 5)
    if ($request->hasFile('jsa')) {
        // Delete old JSA files if updating
        if ($dataSib) {
            for ($i = 1; $i <= 5; $i++) {
                $path = "jsa_path$i";
                if ($dataSib->$path) {
                    Storage::delete($dataSib->$path);
                }
            }
        }

        foreach ($request->file('jsa') as $key => $file) {
            if ($key < 5) { // Max 5 files
                $storagePath = self::BASE_STORAGE_PATH . 'jsas';
                $filename = $file->hashName();
                $path = $file->storeAs($storagePath, $filename);
                $data["jsa_path".($key+1)] = str_replace('public/', '', $path);
            }
        }
    }

    // Handle IBPR files (max 5)
    if ($request->hasFile('ibpr')) {
        // Delete old IBPR files if updating
        if ($dataSib) {
            for ($i = 1; $i <= 5; $i++) {
                $path = "ibpr_path$i";
                if ($dataSib->$path) {
                    Storage::delete($dataSib->$path);
                }
            }
        }

        foreach ($request->file('ibpr') as $key => $file) {
            if ($key < 5) { // Max 5 files
                $storagePath = self::BASE_STORAGE_PATH . 'ibprs';
                $filename = $file->hashName();
                $path = $file->storeAs($storagePath, $filename);
                $data["ibpr_path".($key+1)] = str_replace('public/', '', $path);
            }
        }
    }
}

    /**
     * Delete all files associated with a DataSib record
     */
    private function deleteAllFiles(DataSib $dataSib)
    {
        // Single file fields
        $singleFileFields = [
            'work_permit_path',
            'emergency_preparedness_path',
            'emergency_escape_plan_path',
            'staggling_plan_path',
            'history_training_path',
            'kajian_geotek_path',
            'form_fpp_path',
            'form_observasi_berjenjang_path',
            'form_p2h_unit_lifting_path',
            'form_inspeksi_tools_path',
        ];

        foreach ($singleFileFields as $field) {
            if ($dataSib->$field) {
                Storage::delete($dataSib->$field);
            }
        }

        // Multiple JSA and IBPR files
        for ($i = 1; $i <= 5; $i++) {
            $jsaPath = "jsa_path$i";
            $ibprPath = "ibpr_path$i";

            if ($dataSib->$jsaPath) {
                Storage::delete($dataSib->$jsaPath);
            }

            if ($dataSib->$ibprPath) {
                Storage::delete($dataSib->$ibprPath);
            }
        }
    }

    /**
     * Authorization check for viewing
     */
    private function authorizeView(DataSib $dataSib)
    {
        if (Auth::user()->code_role != '001' && $dataSib->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
