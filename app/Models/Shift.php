<?php
namespace App\Models;

use App\Models\FatigueActivity;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'data_shift';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    public function fatigueActivities()
    {
        return $this->hasMany(FatigueActivity::class);
    }
}
