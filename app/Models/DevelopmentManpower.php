<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentManpower extends Model
{
    use HasFactory;

    protected $table = 'development_manpower';

    protected $fillable = [
        'kategori_aktivitas',
        'posisi',
        'foto_aktivitas',
        'dokumen_1',
        'dokumen_2',
        'deskripsi',
        'pengawas_id',
        'pelaku_korban_id',
        'saksi_id',
        'kronologi',
        'tanggal_aktivitas',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal_aktivitas' => 'date',
    ];

    // Kategori aktivitas yang tersedia
    const KATEGORI_AKTIVITAS = [
        'SKKP/POP For GL Mitra',
        'Training HRCP Mitra',
        'Training Additional Plant',
        'Review IBPR',
        'Review SMKP For Mitra Kerja',
        'Pembinaan Pelanggaran'
    ];

    // Relasi dengan pengawas
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    // Relasi dengan pelaku/korban
    public function pelakuKorban()
    {
        return $this->belongsTo(User::class, 'pelaku_korban_id');
    }

    // Relasi dengan saksi
    public function saksi()
    {
        return $this->belongsTo(User::class, 'saksi_id');
    }

    // Accessor untuk nama kategori yang lebih bersih
    public function getKategoriAktivitasFormattedAttribute()
    {
        return str_replace('_', ' ', $this->kategori_aktivitas);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori_aktivitas', $kategori);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeTanggalRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_aktivitas', [$start, $end]);
    }
}
