<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relationships
     */
    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic', 'id');
    }

    public function fatigueActivities()
    {
        return $this->hasMany(FatigueActivity::class, 'mitra_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeInactive(Builder $query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where('nama_perusahaan', 'like', '%'.$search.'%')
                    ->orWhere('alamat', 'like', '%'.$search.'%');
    }

    public function scopeWithPicInfo(Builder $query)
    {
        return $query->with(['picUser' => function($q) {
            $q->select('id', 'name', 'email');
        }]);
    }

    /**
     * Accessors
     */
    public function getFullAddressAttribute()
    {
        return $this->alamat;
    }

    public function getPicNameAttribute()
    {
        return $this->picUser ? $this->picUser->name : 'N/A';
    }

    /**
     * Mutators
     */
    public function setNamaPerusahaanAttribute($value)
    {
        $this->attributes['nama_perusahaan'] = ucwords(strtolower($value));
    }


    /**
     * Helper Methods
     */
    public function isActive()
    {
        return is_null($this->deleted_at);
    }

    public function getStatusAttribute()
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    public static function dropdownOptions()
    {
        return self::active()
                ->orderBy('nama_perusahaan')
                ->pluck('nama_perusahaan', 'id')
                ->prepend('Pilih Mitra', '');
    }
}
