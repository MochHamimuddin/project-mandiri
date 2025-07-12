<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataLaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_laporan';

    protected $fillable = [
        'is_upload',
        'user_id',
        'deadline_time',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeNeedNotification($query)
    {
        return $query->where('is_upload', 0)
                    ->where('deadline_time', '<=', now('Asia/Jakarta'))
                    ->whereNull('deleted_at');
    }
}
