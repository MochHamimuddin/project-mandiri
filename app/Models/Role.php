<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'data_role';
    protected $fillable = ['code_role', 'nama_role'];
    public $timestamps = false;
}
