<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsappNotificationService;

class FirePreventiveManagement extends Model
{
    use HasFactory, SoftDeletes;

    // Activity Type Constants
    const TYPE_PENCUCIAN_UNIT = 'Pencucian Unit';
    const TYPE_INSPEKSI_APAR = 'Inspeksi APAR';

    // Monthly Deadlines (WIB Time) - tanggal 28 setiap bulan
    public static $monthlyDeadlines = [
        self::TYPE_PENCUCIAN_UNIT => '00:22', // Jam 8 pagi
        self::TYPE_INSPEKSI_APAR => '00:24'   // Jam 9 pagi
    ];

    protected $table = 'fire_preventive_management';

    protected $fillable = [
        'id',
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

    protected $appends = [
        'activity_type_name',
        'deadline_time_wib',
        'is_uploaded',
        'should_send_notification'
    ];

    // ==================== NOTIFICATION LOGIC ====================
    public function shouldSendNotification(): bool
    {
        // Skip jika sudah upload foto atau form
        if ($this->is_uploaded) {
            return false;
        }

        // Skip jika supervisor tidak ada nomor WA
        if (empty($this->supervisor->no_telp)) {
            return false;
        }

        $now = now('Asia/Jakarta');
        $currentMonthDeadline = $this->getCurrentMonthDeadline();

        // Hanya kirim jika sekarang sudah lewat deadline bulan ini
        if ($now->lessThan($currentMonthDeadline)) {
            return false;
        }

        // Cek apakah sudah pernah dikirim bulan ini
        $cacheKey = $this->getNotificationCacheKey();
        return !Cache::has($cacheKey);
    }

    protected function getNotificationCacheKey(): string
    {
        return sprintf('fire_preventive_notif_%s_%s_%s',
            $this->id,
            $this->activity_type,
            now('Asia/Jakarta')->format('Y-m')
        );
    }

    public function sendNotification(): bool
    {
        if (!$this->shouldSendNotification()) {
            return false;
        }

        $service = new WhatsappNotificationService();
        $message = $this->getNotificationMessage();

        if ($service->sendMessage($this->supervisor->no_telp, $message)) {
            Cache::put(
                $this->getNotificationCacheKey(),
                true,
                now()->addMonth() // Cache berlaku sampai bulan berikutnya
            );
            return true;
        }

        return false;
    }

    protected function getNotificationMessage(): string
    {
        return sprintf(
            "⚠️ **Peringatan!** Aktivitas %s belum dilaporkan untuk bulan ini. Deadline: %s WIB",
            $this->activity_type_name,
            $this->getCurrentMonthDeadline()->format('d M Y H:i')
        );
    }

    /**
     * Get current month's deadline (tanggal 28 dengan jam sesuai aktivitas)
     */
    protected function getCurrentMonthDeadline(): Carbon
    {
        $deadlineTime = self::$monthlyDeadlines[$this->activity_type] ?? '06:00';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::now('Asia/Jakarta')
            ->setDay(17) // Tanggal 28
            ->setTime($hours, $minutes, 0);
    }

    // ==================== ACCESSORS ====================
    public function getActivityTypeNameAttribute(): string
    {
        // return $this->activity_type === self::TYPE_PENCUCIAN_UNIT
        //     ? self::TYPE_PENCUCIAN_UNIT
        //     : self::TYPE_INSPEKSI_APAR;

        return $this->activity_type === 'Pencucian Unit'
        ? 'Pencucian Unit'
        : 'Inspeksi APAR';
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getCurrentMonthDeadline()->format('d M Y H:i') . ' WIB';
    }

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->foto_path) || !empty($this->form_fpp_path);
    }

    public function getShouldSendNotificationAttribute(): bool
    {
        return $this->shouldSendNotification();
    }

    // ==================== RELATIONSHIPS ====================
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ==================== QUERY SCOPES ====================
    public function scopePencucianUnit($query)
    {
        return $query->where('activity_type', 'Pencucian Unit');
    }

    public function scopeInspeksiApar($query)
    {
        return $query->where('activity_type', 'Inspeksi APAR');
    }

    // public function scopeDueForNotification($query)
    // {
    //     $now = now('Asia/Jakarta');

    //     return $query->whereHas('supervisor', function($q) {
    //             $q->whereNotNull('no_telp');
    //         })
    //         ->where(function($q) {
    //             $q->whereNull('foto_path')
    //               ->orWhereNull('form_fpp_path');
    //         })
    //         ->where(function($q) use ($now) {
    //             foreach (self::$monthlyDeadlines as $type => $time) {
    //                 [$hours, $minutes] = explode(':', $time);
    //                 $deadline = Carbon::now('Asia/Jakarta')
    //                     ->setDay(28)
    //                     ->setTime($hours, $minutes, 0);
                    
    //                 if ($now->greaterThanOrEqualTo($deadline)) {
    //                     $q->orWhere('activity_type', $type);
    //                 }
    //             }
    //         });
    // }
}