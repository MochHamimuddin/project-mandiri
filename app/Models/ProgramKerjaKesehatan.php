<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramKerjaKesehatan extends Model
{
    use HasFactory, SoftDeletes;

    // Konstanta untuk jenis program
    const MCU_TAHUNAN = 'MCU_TAHUNAN';
    const PENYAKIT_KRONIS = 'PENYAKIT_KRONIS';

    protected $table = 'program_kerja_kesehatan';

    protected $fillable = [
        'jenis_program',
        'foto_path',
        'dokumen_path',
        'deskripsi',
        'pengawas_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'tanggal_upload' // Pastikan ini ada di fillable jika digunakan
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    protected $appends = ['jenis_program_label'];

    /**
     * Accessor untuk label jenis program
     */
    public function getJenisProgramLabelAttribute()
    {
        return [
            self::MCU_TAHUNAN => 'MCU Tahunan',
            self::PENYAKIT_KRONIS => 'Penyakit Kronis'
        ][$this->jenis_program] ?? 'Unknown Program';
    }

    /**
     * Relasi ke user sebagai pengawas
     */
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    /**
     * Relasi ke user yang membuat record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang terakhir mengupdate record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk MCU Tahunan
     */
    public function scopeMcuTahunan($query)
    {
        return $query->where('jenis_program', self::MCU_TAHUNAN);
    }

    /**
     * Scope untuk Penyakit Kronis
     */
    public function scopePenyakitKronis($query)
    {
        return $query->where('jenis_program', self::PENYAKIT_KRONIS);
    }

    /**
     * Daftar opsi jenis program untuk form select
     */
    public static function getJenisProgramOptions()
    {
        return [
            self::MCU_TAHUNAN => 'MCU Tahunan',
            self::PENYAKIT_KRONIS => 'Penyakit Kronis',
        ];
    }
}
