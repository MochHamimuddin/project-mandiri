<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataSib extends Model
{
    use HasFactory;

    protected $table = 'data_sib';

    protected $fillable = [
        'nama_lengkap',
        'nrp',
        'departemen',
        'perihal',
        'lokasi',
        'jenis_pekerjaan',
        'tanggal_mulai',
        'tanggal_akhir',
        'pengajuan_baru_h7',
        'perpanjangan_h2',
        'work_permit_path',
        'jsa_path1',
        'jsa_path2',
        'jsa_path3',
        'jsa_path4',
        'jsa_path5',
        'ibpr_path1',
        'ibpr_path2',
        'ibpr_path3',
        'ibpr_path4',
        'ibpr_path5',
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

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants for ENUM values
    public const DEPARTEMEN = [
        'EngProd 1' => 'EngProd 1',
        'EngProd 2' => 'EngProd 2',
        'Plant' => 'Plant',
        'SM' => 'SM',
        'HCGS-FAT' => 'HCGS-FAT',
        'ICT' => 'ICT',
        'SHE' => 'SHE',
    ];

    public const PERIHAL = [
        'Pengajuan SIB Baru' => 'Pengajuan SIB Baru',
        'Perpanjangan SIB' => 'Perpanjangan SIB',
    ];

    public const LOKASI = [
        'Blok 2' => 'Blok 2',
        'Blok 3' => 'Blok 3',
    ];

    public const JENIS_PEKERJAAN = [
        'Dumping & Loading HRA' => 'Dumping & Loading HRA',
        'Aktifitas Peledakan' => 'Aktifitas Peledakan',
        'Bekerja di Ketinggian >1.8 meter' => 'Bekerja di Ketinggian >1.8 meter',
        'Bekerja di Dekat Air' => 'Bekerja di Dekat Air',
        'Bekerja Kelistrikan >380 V' => 'Bekerja Kelistrikan >380 V',
        'Pengangkatan/Lifting' => 'Pengangkatan/Lifting',
        'Bekerja di Ruang Terbatas' => 'Bekerja di Ruang Terbatas',
        'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3' => 'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3',
        'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang' => 'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
        'Pekerjaan Eksplorasi Area Kritis' => 'Pekerjaan Eksplorasi Area Kritis',
        'Aktifitas Land Clearing' => 'Aktifitas Land Clearing',
        'Maintenance Conveyor' => 'Maintenance Conveyor',
        'Penggalian/Gangguan di Sekitar Bangunan' => 'Penggalian/Gangguan di Sekitar Bangunan',
        'Aktifitas Pengelasan Bahan Mudah Terbakar' => 'Aktifitas Pengelasan Bahan Mudah Terbakar',
    ];



    public const YA_TIDAK = [
        'Ya' => 'Ya',
        'Tidak' => 'Tidak',
    ];

    // Helper methods for file paths
    public function getJsaPaths()
    {
        return array_filter([
            $this->jsa_path1,
            $this->jsa_path2,
            $this->jsa_path3,
            $this->jsa_path4,
            $this->jsa_path5,
        ]);
    }

    public function getIbprPaths()
    {
        return array_filter([
            $this->ibpr_path1,
            $this->ibpr_path2,
            $this->ibpr_path3,
            $this->ibpr_path4,
            $this->ibpr_path5,
        ]);
    }

    // Validation rules for form submission
    public static function rules($id = null)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:100',
            'nrp' => 'required|string|max:20',
            'departemen' => 'required|in:' . implode(',', array_keys(self::DEPARTEMEN)),
            'perihal' => 'required|in:' . implode(',', array_keys(self::PERIHAL)),
            'lokasi' => 'required|in:' . implode(',', array_keys(self::LOKASI)),
            'jenis_pekerjaan' => 'required|in:' . implode(',', array_keys(self::JENIS_PEKERJAAN)),
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'pengajuan_baru_h7' => 'required|in:Ya,Tidak',
            'perpanjangan_h2' => 'required|in:Ya,Tidak',

            // File validation rules
            'work_permit' => 'required|file|mimes:pdf|max:10240',
            'jsa.*' => 'nullable|file|mimes:pdf|max:10240',
            'ibpr.*' => 'nullable|file|mimes:pdf|max:10240',
            'emergency_preparedness' => 'required|file|mimes:pdf|max:10240',
            'emergency_escape_plan' => 'required|file|mimes:pdf|max:10240',
            'history_training' => 'required|file|mimes:pdf|max:10240',
            'form_fpp' => 'required|file|mimes:pdf|max:10240',
            'form_observasi_berjenjang' => 'required|file|mimes:pdf|max:10240',
        ];

        // Conditional validation based on jenis_pekerjaan
        $rules['staggling_plan'] = [
            'nullable',
            'exclude_unless:jenis_pekerjaan,' . implode(',', [
                'Dumping & Loading HRA',
                'Aktifitas Peledakan',
                'Bekerja di Ruang Terbatas',
                'Pekerjaan Eksplorasi Area Kritis',
                'Aktifitas Land Clearing',
                'Aktifitas Pengelasan Bahan Mudah Terbakar'
            ]),
            'file',
            'mimes:pdf',
            'max:10240'
        ];

        $rules['kajian_geotek'] = [
            'nullable',
            'exclude_unless:jenis_pekerjaan,' . implode(',', [
                'Dumping & Loading HRA',
                'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3',
                'Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang',
                'Aktifitas Land Clearing',
                'Aktifitas Pengelasan Bahan Mudah Terbakar'
            ]),
            'required',
            'file',
            'mimes:pdf',
            'max:10240'
        ];

        $rules['form_p2h_unit_lifting'] = [
            'nullable',
            'exclude_unless:jenis_pekerjaan,Pengangkatan/Lifting',
            'required',
            'file',
            'mimes:pdf',
            'max:10240'
        ];

        $rules['form_inspeksi_tools'] = [
            'nullable',
            'exclude_unless:jenis_pekerjaan,Pengangkatan/Lifting',
            'required',
            'file',
            'mimes:pdf',
            'max:10240'
        ];

        return $rules;
    }

    // Custom validation messages
    public static function messages()
    {
        return [
            'kajian_geotek.required' => 'Kajian geotek wajib diunggah untuk jenis pekerjaan ini.',
            'form_p2h_unit_lifting.required' => 'Form P2H Unit Lifting wajib diunggah untuk pekerjaan lifting.',
            'form_inspeksi_tools.required' => 'Form Inspeksi Tools wajib diunggah untuk pekerjaan lifting.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai.',
        ];
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
