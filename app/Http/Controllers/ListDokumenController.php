<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ListDokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class ListDokumenController extends Controller
{

    private const BASE_STORAGE_PATH = 'list_dokumen/';

        /**
     * Menampilkan semua data dalam tabel
     */
    public function index()
    {
        // For role 001 (admin) show all data
        if (Auth::user()->code_role == '001') {
            $activities = ListDokumen::with(['creator', 'user'])->latest()->paginate(10);
        } else {
            // For role 002 (user) only show their own data
            $activities = ListDokumen::where('created_by', Auth::id())->latest()->paginate(10);
        }

        return view('list-dokumen.index', compact('activities'));
    }

        /**
     * Menampilkan form create berdasarkan kategori
     */
    public function create()
    {
        // if (!in_array($kategori, ListDokumen::KATEGORI_AKTIVITAS)) {
        //     abort(404);
        // }

        $users = User::select('id', 'nama_lengkap')->get(); // Ambil hanya id dan nama
        // return view('list-dokumen.create', compact('kategori', 'users'));

        return view('list-dokumen.create', [
            'ListPekerjaan' => ListDokumen::KATEGORI_AKTIVITAS,
            'ListUser' => $users
        ]);
    }

        /**
     * Menyimpan data baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate(ListDokumen::rules());

        $userId = $request['nama_lengkap'];

        // Prepare data without files
        $data = $request->except([
            'file_apd', 'file_p5m_jsa', 'file_p2h', 'file_inspeksi_fpp', 'file_form_fpp',
            'file_kegiatan_berlangsung', 'file_absensi_p5m', 'file_inspeksi_observasi',
        ]);

        $data['id_users'] = $userId;

        // Set creator
        $data['created_by'] = Auth::id();

        // Handle dates (asumsi input format Y-m-d)
        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $data['start_date'] = $tanggalMulai->format('Y-m-d');
        $data['end_date'] = $tanggalMulai->copy()->addDays(7)->format('Y-m-d');
    

        // Handle file uploads
        $this->handleFileUploads($request, $data);

        \Log::debug('Data sebelum create:', $data);

        // Create record
        ListDokumen::create($data);

        // if (auth()->user()->code_role === '002') {
        //     return redirect()->route('daftar-laporan')
        //                     ->with('success', 'Data SIB berhasil dibuat.');
        // }

        return redirect()->route('list-dokumen.index')
                        ->with('success', 'Data Dokumen berhasil ditambahkan.');
    }

        /**
     * Menampilkan detail
     */
    public function show(ListDokumen $listDokumen)
    {
        // $this->authorizeView($list_dokumen);

        // return view('list-dokumen.show', ['listDokumen' => $list_dokumen]);
    
        $listDokumen->loadMissing('user'); // Jika butuh relasi user
    
        \Log::debug('Data dari DB:', [
        'start_date' => $listDokumen->start_date,
        'end_date' => $listDokumen->end_date,
        'raw_attributes' => $listDokumen->getAttributes()
    ]);

    return view('list-dokumen.show', compact('listDokumen'));
    
    }


    public function destroy(ListDokumen $listDokumen)
    {
        // Only admin can delete
        if (Auth::user()->code_role != '001') {
            abort(403, 'Unauthorized action.');
        }

        // Delete all associated files
        $this->deleteAllFiles($listDokumen);

        $listDokumen->delete();

        return redirect()->route('list-dokumen.index')
                        ->with('success', 'Data Dokumen berhasil dihapus.');
    }

    private function validateRequest(Request $request, $model = null)
    {
        $rules = [
            'kategori_aktivitas' => 'required|in:' . implode(',', ListDokumen::KATEGORI_AKTIVITAS),
            'tanggal_mulai' => 'required|date',
        ];

        // // Aturan file untuk edit (nullable)
        // $fileRules = [
        //     'foto_aktivitas' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        //     'dokumen_1' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        //     'dokumen_2' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
        // ];

        // // Gabungkan aturan
        // $rules = array_merge($rules, $fileRules);

        // // Aturan khusus berdasarkan kategori
        // switch ($request->kategori_aktivitas) {
        //     case 'SKKP/POP For GL Mitra':
        //     case 'Training HRCP Mitra':
        //         $rules['posisi'] = 'required|string|max:100';
        //         break;

        //     case 'Pembinaan Pelanggaran':
        //         $rules['pelaku_korban_id'] = 'required|exists:data_users,id';
        //         $rules['saksi_id'] = 'nullable|exists:data_users,id';
        //         $rules['kronologi'] = 'required|string|max:2000';
        //         break;
        // }

        return $request->validate($rules);
    }


    private function handleFileUploads(Request $request, array &$data, ListDokumen $listDokumen = null)
    {
        // Define all file fields and their storage paths
        $fileFields = [
            'file_apd' => ['path' => 'file_apd', 'db_field' => 'path_apd'],
            'file_p5m_jsa' => ['path' => 'file_p5m_jsa', 'db_field' => 'path_p5m_jsa'],
            'file_p2h' => ['path' => 'file_p2h', 'db_field' => 'path_p2h'],
            'file_inspeksi_fpp' => ['path' => 'file_inspeksi_fpp', 'db_field' => 'path_fpp'],
            'file_form_fpp' => ['path' => 'file_form_fpp', 'db_field' => 'path_form_fpp'],
            'file_kegiatan_berlangsung' => ['path' => 'file_kegiatan_berlangsung', 'db_field' => 'path_kegiatan'],
            'file_absensi_p5m' => ['path' => 'file_absensi_p5m', 'db_field' => 'path_absensi_p5m'],
            'file_inspeksi_observasi' => ['path' => 'file_inspeksi_observasi', 'db_field' => 'path_inspeksi'],
        ];
    
        // Handle single file uploads
        foreach ($fileFields as $field => $config) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($listDokumen && $listDokumen->{$config['db_field']}) {
                    Storage::delete($listDokumen->{$config['db_field']});
                }
    
                $storagePath = self::BASE_STORAGE_PATH . $config['path'];
                $uploadedFile = $request->file($field);
    
                // Generate unique filename with original extension
                $filename = $uploadedFile->hashName();
                // $path = $uploadedFile->storeAs($storagePath, $filename);
                $path = $uploadedFile->storeAs(
                    'public/' . self::BASE_STORAGE_PATH . $config['path'],
                    $filename
                );
    
                // Store relative path (without 'public/' prefix)
                $data[$config['db_field']] = str_replace('public/', '', $path);
            }
        }
    }

    private function deleteAllFiles(ListDokumen $listDokumen)
    {
        // Single file fields
        $singleFileFields = [
            'path_apd',
            'path_p5m_jsa',
            'path_p2h',
            'path_fpp',
            'path_form_fpp',
            'path_kegiatan',
            'path_absensi_p5m',
            'path_inspeksi',
        ];

        foreach ($singleFileFields as $field) {
            if ($listDokumen->$field) {
                Storage::delete($listDokumen->$field);
            }
        }
    }


    private function authorizeView(ListDokumen $listDokumen)
    {
        if (Auth::user()->code_role != '001' && $listDokumen->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }


}


?>