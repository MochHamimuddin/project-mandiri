<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use SoftDeletes;

    protected $table = 'data_mitra';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'pic',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'pic' => 'integer',
    ];

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic', 'id');
    }
}
