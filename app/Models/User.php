<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'data_users';

    protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'password',
        'no_telp',
        'code_role',
        'updated_at',
        'updated_by',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'code_role', 'code_role');
    }
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }
    // Di dalam App\Models\User

    // Relasi sebagai pengawas
    public function pengawasanManpower()
    {
        return $this->hasMany(DevelopmentManpower::class, 'pengawas_id');
    }

    // Relasi sebagai pelaku/korban
    public function sebagaiPelakuKorban()
    {
        return $this->hasMany(DevelopmentManpower::class, 'pelaku_korban_id');
    }

    // Relasi sebagai saksi
    public function sebagaiSaksi()
    {
        return $this->hasMany(DevelopmentManpower::class, 'saksi_id');
    }
}

