<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirePreventiveManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fire_preventive_management';

    protected $fillable = [
        'activity_type',
        'foto_path',
        'form_fpp_path',
        'inspection_location',
        'description',
        'supervisor_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relasi ke user sebagai supervisor/pengawas
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Relasi ke user yang membuat record
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang terakhir mengupdate record
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi ke user yang menghapus record
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scope untuk pencucian unit
    public function scopePencucianUnit($query)
    {
        return $query->where('activity_type', 'Pencucian Unit');
    }

    // Scope untuk inspeksi APAR
    public function scopeInspeksiApar($query)
    {
        return $query->where('activity_type', 'Inspeksi APAR');
    }

    // Accessor untuk activity type
    public function getActivityTypeNameAttribute()
    {
        return $this->activity_type === 'Pencucian Unit'
            ? 'Pencucian Unit'
            : 'Inspeksi APAR';
    }
}
