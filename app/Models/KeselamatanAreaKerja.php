<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeselamatanAreaKerja extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terkait dengan model
     */
    protected $table = 'keselamatan_area_kerja';

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = [
        'activity_type',
        'pengawas_id',
        'mitra_id',
        'deskripsi',
        'path_foto',
        'path_file',
        'is_approved',
        'created_by',
        'updated_by'
    ];

    /**
     * Kolom yang harus disembunyikan dari array/JSON
     */
    protected $hidden = [
        'deleted_at',
        'deleted_by'
    ];

    /**
     * Tipe data casting
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const TYPE_INSPEKSI_OBSERVASI = 'Inspeksi & Observasi Tematik';
    const TYPE_GELAR_INSPEKSI = 'Gelar/Inspeksi Tools';
    const TYPE_HOUSEKEEPING = 'Penilaian Kondisi Fisik/Housekeeping Workshop Mitra';

    /**
     * Daftar tipe aktivitas yang valid
     */
    public static function getActivityTypes()
    {
        return [
            self::TYPE_INSPEKSI_OBSERVASI => 'Inspeksi & Observasi Tematik',
            self::TYPE_GELAR_INSPEKSI => 'Gelar/Inspeksi Tools',
            self::TYPE_HOUSEKEEPING => 'Penilaian Kondisi Fisik/Housekeeping'
        ];
    }

    /**
     * Scope untuk tipe aktivitas tertentu
     */
    public function scopeInspeksiObservasi($query)
    {
        return $query->where('activity_type', self::TYPE_INSPEKSI_OBSERVASI);
    }

    public function scopeGelarInspeksi($query)
    {
        return $query->where('activity_type', self::TYPE_GELAR_INSPEKSI);
    }

    public function scopeHousekeeping($query)
    {
        return $query->where('activity_type', self::TYPE_HOUSEKEEPING);
    }

    /**
     * Relasi ke model User (pengawas)
     */
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
