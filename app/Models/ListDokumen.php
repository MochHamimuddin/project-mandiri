<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListDokumen extends Model
{
    use HasFactory;

    protected $table = 'list_dokumen';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id_users',
        'jenis_pekerjaan',
        'start_date',
        'end_date',
        'path_apd',
        'path_p5m_jsa',
        'path_p2h',
        'path_fpp',
        'path_form_fpp',
        'path_kegiatan',
        'path_absensi_p5m',
        'path_inspeksi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        // field date lainnya
    ];

    const KATEGORI_AKTIVITAS = [
        'Dumping & Loading HRA' => 'Dumping & Loading HRA',
        'Aktifitas Peledakan' => 'Aktifitas Peledakan',
        'Bekerja di Ketinggian >1.8 meter' => 'Bekerja di Ketinggian >1.8 meter',
        'Bekerja di Dekat Air' => 'Bekerja di Dekat Air',
        'Bekerja Kelistrikan >380 V' => 'Bekerja Kelistrikan >380 V',
        'Pengangkatan (Lifting)' => 'Pengangkatan (Lifting)',
        'Bekerja di ruang terbatas' => 'Bekerja di ruang terbatas',
        'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3' => 'Bekerja di Dekat/Bawah Tebing Rawan Longsor FK<1.3',
        'Pelepasan dan pemasangan tyre HD di jalan tambang' => 'Pelepasan dan pemasangan tyre HD di jalan tambang',
        'Pekerjaan Eksplorasi Area Kritis' => 'Pekerjaan Eksplorasi Area Kritis',
        'Aktifitas Land Clearing' => 'Aktifitas Land Clearing',
        'Maintenance Conveyor' => 'Maintenance Conveyor',
        'Penggalian/Gangguan di Sekitar Bangunan' => 'Penggalian/Gangguan di Sekitar Bangunan',
        'Aktivitas Pengelasan dekat bahan mudah terbakar' => 'Aktivitas Pengelasan dekat bahan mudah terbakar',
    ];

    // public function pengawas()
    // {
    //     return $this->belongsTo(User::class, '');
    // }

    public static function rules($id = null)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:100',
            'jenis_pekerjaan' => 'required|in:' . implode(',', array_keys(self::KATEGORI_AKTIVITAS)),
            'tanggal_mulai' => 'required|date',

            // File validation rules
            'file_apd' => 'nullable|file|mimes:pdf|max:10240',
            'file_p5m_jsa' => 'nullable|file|mimes:pdf|max:10240',
            'file_p2h' => 'nullable|file|mimes:pdf|max:10240',
            'file_inspeksi_fpp' => 'nullable|file|mimes:pdf|max:10240',
            'file_form_fpp' => 'nullable|file|mimes:pdf|max:10240',
            'file_kegiatan_berlangsung' => 'nullable|file|mimes:pdf|max:10240',
            'file_absensi_p5m' => 'nullable|file|mimes:pdf|max:10240',
            'file_inspeksi_observasi' => 'nullable|file|mimes:pdf|max:10240',
        ];

        return $rules;
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
        // Parameter kedua adalah foreign key di tabel list_dokumen
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}

?>