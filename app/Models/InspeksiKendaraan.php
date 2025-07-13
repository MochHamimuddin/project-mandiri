<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class InspeksiKendaraan extends Model
{
    use SoftDeletes;

    protected $table = 'inspeksi_kendaraan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jenis_inspeksi',
        'tanggal_inspeksi',
        'deskripsi',
        'pengawas_id',
        'mitra_id',
        'jenis_komisioning',
        'jadwal_perawatan',
        'pelaksana_perawatan',
        'hasil_observasi_kecepatan',
        'satuan_kecepatan',
        'path_foto',
        'path_dokumen'
    ];

    protected $dates = [
        'tanggal_inspeksi',
        'jadwal_perawatan',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $casts = [
        'tanggal_inspeksi' => 'date',
        'jadwal_perawatan' => 'date',
    ];

    protected $appends = [
        'hasil_observasi_lengkap',
        'foto_url',
        'dokumen_url'
    ];

    /**
     * Relasi dengan pengawas (user)
     */
    /**
 * Relasi dengan pengawas (user)
 */
// Di InspeksiKendaraan.php
public function pengawas()
{
    return $this->belongsTo(User::class, 'pengawas_id')->withDefault([
        'nama_lengkap' => 'Tidak Diketahui'
    ]);
}



public function mitra()
{
    return $this->belongsTo(Mitra::class, 'mitra_id')->withDefault([
        'nama_perusahaan' => 'Tidak Diketahui'
    ]);
}

    /**
     * Scope untuk komisioning
     */
    public function scopeKomisioning($query)
    {
        return $query->where('jenis_inspeksi', 'komisioning');
    }

    /**
     * Scope untuk perawatan
     */
    public function scopePerawatan($query)
    {
        return $query->where('jenis_inspeksi', 'perawatan');
    }

    /**
     * Scope untuk evaluasi kecepatan
     */
    public function scopeEvaluasiKecepatan($query)
    {
        return $query->where('jenis_inspeksi', 'evaluasi_kecepatan');
    }

    /**
     * Accessor untuk hasil observasi lengkap
     */
    public function getHasilObservasiLengkapAttribute(): ?string
    {
        return $this->hasil_observasi_kecepatan && $this->satuan_kecepatan
            ? "{$this->hasil_observasi_kecepatan} {$this->satuan_kecepatan}"
            : null;
    }

    /**
     * Accessor untuk URL foto
     */
   public function getFotoUrlAttribute()
{
    if (!$this->path_foto) {
        return asset('images/default-image.jpg');
    }

    // Pastikan path sudah benar
    if (str_starts_with($this->path_foto, 'public/')) {
        return asset(str_replace('public/', 'storage/', $this->path_foto));
    }

    return asset('storage/'.$this->path_foto);
}

public function getDokumenUrlAttribute()
{
    if (!$this->path_dokumen) {
        return null;
    }

    if (str_starts_with($this->path_dokumen, 'public/')) {
        return asset(str_replace('public/', 'storage/', $this->path_dokumen));
    }

    return asset('storage/'.$this->path_dokumen);
}

    /**
     * Mutator untuk tanggal inspeksi
     */
    public function setTanggalInspeksiAttribute($value): void
    {
        $this->attributes['tanggal_inspeksi'] = Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * Mutator untuk jadwal perawatan
     */
    public function setJadwalPerawatanAttribute($value): void
    {
        if ($value) {
            $this->attributes['jadwal_perawatan'] = Carbon::parse($value)->format('Y-m-d');
        }
    }

    /**
     * Mutator untuk hasil observasi kecepatan
     */
    public function setHasilObservasiKecepatanAttribute($value): void
    {
        $this->attributes['hasil_observasi_kecepatan'] = preg_replace('/[^0-9.]/', '', $value);
    }

    /**
     * Format tanggal untuk tampilan
     */
    public function getTanggalInspeksiFormattedAttribute(): string
    {
        return Carbon::parse($this->tanggal_inspeksi)->format('d M Y');
    }

    /**
     * Format jadwal perawatan untuk tampilan
     */
    public function getJadwalPerawatanFormattedAttribute(): ?string
    {
        return $this->jadwal_perawatan
            ? Carbon::parse($this->jadwal_perawatan)->format('d M Y')
            : null;
    }
}
