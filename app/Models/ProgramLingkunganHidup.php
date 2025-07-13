<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramLingkunganHidup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'program_lingkungan_hidup';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jenis_kegiatan',
        'upload_foto',
        'deskripsi',
        'detail_temuan',
        'tindakan_perbaikan',
        'tanggal_kegiatan',
        'lokasi',
        'pelaksana',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'tanggal_kegiatan',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relationship with User who created the record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with User who last updated the record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship with User who deleted the record
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope for Krida Area activities
     */
    public function scopeKridaArea($query)
    {
        return $query->where('jenis_kegiatan', 'Krida Area Office/Workshop');
    }

    /**
     * Scope for Pengelolaan Lingkungan activities
     */
    public function scopePengelolaanLingkungan($query)
    {
        return $query->where('jenis_kegiatan', 'Pengelolaan Lingkungan Workshop');
    }
}
