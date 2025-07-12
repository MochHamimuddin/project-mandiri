<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsappNotificationService;

class FatigueActivity extends Model
{
    use SoftDeletes;

    // Activity Type Constants
    const TYPE_FTW = 'ftw';
    const TYPE_DFIT = 'dfit';
    const TYPE_FATIGUE_CHECK = 'fatigue_check';
    const TYPE_WAKEUP_CALL = 'wakeup_call';
    const TYPE_SAGA = 'saga';
    const TYPE_SIDAK = 'sidak';
    

    public static $typeLabels = [
    self::TYPE_FTW => 'First Time Work (FTW)',
    self::TYPE_DFIT => 'Evaluasi D-Fit',
    self::TYPE_FATIGUE_CHECK => 'Fatigue Check',
    self::TYPE_WAKEUP_CALL => 'Wake Up Call',
    self::TYPE_SAGA => 'Inspeksi SAGA',
    self::TYPE_SIDAK => 'Sidak Napping'
];
public static $validActivityTypes = [
    self::TYPE_FTW,
    self::TYPE_DFIT,
    self::TYPE_FATIGUE_CHECK,
    self::TYPE_WAKEUP_CALL,
    self::TYPE_SAGA,
    self::TYPE_SIDAK
];

    // Activity Deadlines (WIB Time)
    protected static $activityDeadlines = [
        self::TYPE_FTW => '03:00',
        self::TYPE_DFIT => '07:00',
        self::TYPE_FATIGUE_CHECK => '08:00',
        self::TYPE_WAKEUP_CALL => '09:00',
        self::TYPE_SAGA => '10:00',
        self::TYPE_SIDAK => '11:00'
    ];

    protected $table = 'fatigue_activities';

    protected $fillable = [
        'activity_type',
        'user_id',
        'supervisor_id',
        'shift_id',
        'mitra_id',
        'photo_path',
        'result_path',
        'description',
        'is_approved',
        'location',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $appends = [
        'activity_type_label',
        'deadline_time_wib',
        'is_uploaded',
        'should_send_notification'
    ];

    // ==================== CORE METHODS ====================
    public function shouldSendNotification(): bool
{
    // 1. Skip jika sudah di-upload atau tidak ada nomor telepon
    if ($this->is_uploaded || empty($this->user->no_telp)) {
        return false;
    }

    $deadline = $this->getDeadlineInCarbon();
    $now = now('Asia/Jakarta');

    // 2. Jika sudah melewati deadline, STOP notifikasi
    if ($now->greaterThanOrEqualTo($deadline)) {
        logger()->info("Deadline passed for activity {$this->id}");
        return false;
    }

    // 3. Kirim notifikasi hanya dalam 30 menit terakhir sebelum deadline
    $notificationWindowStart = $deadline->copy()->subMinutes(30);
    return $now->between($notificationWindowStart, $deadline);
}

public function sendNotification(): bool
{
    if (!$this->shouldSendNotification()) {
        logger()->info("Notification skipped for activity {$this->id}", [
            'reason' => 'Outside notification window or conditions not met',
            'deadline' => $this->deadline_time_wib,
            'current_time' => now('Asia/Jakarta')->format('Y-m-d H:i:s'),
            'window_start' => $this->getDeadlineInCarbon()->subMinutes(30)->format('Y-m-d H:i:s')
        ]);
        return false;
    }

    $cacheKey = "activity_{$this->activity_type}_{$this->user_id}_last_notification";

    // Cek cache untuk jeda 5 menit (diperpendek dari 30 menit)
    if (Cache::has($cacheKey)) {
        logger()->info("Notification throttled (5-minute cooldown) for activity {$this->id}");
        return false;
    }

    try {
        $service = new WhatsappNotificationService();
        $sent = $service->sendDeadlineReminder(
            $this->user->no_telp,
            $this->activity_type_label,
            $this->deadline_time_wib
        );

        if ($sent) {
            // Set cache hanya untuk 5 menit
            Cache::put($cacheKey, now(), now()->addMinutes(5));
            logger()->info("Notification sent for {$this->activity_type} to {$this->user->no_telp}");
            return true;
        }

        logger()->error("Failed to send notification for {$this->activity_type}");
        return false;

    } catch (\Exception $e) {
        logger()->error("WhatsApp API error: " . $e->getMessage());
        return false;
    }
}

    // ==================== HELPER METHODS ====================
    protected function getDeadlineInCarbon(): Carbon
    {
        $deadlineTime = self::$activityDeadlines[$this->activity_type] ?? '06:00';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::parse($this->created_at)
            ->timezone('Asia/Jakarta')
            ->setTime($hours, $minutes);
    }

    // ==================== ACCESSORS ====================
    public function getActivityTypeLabelAttribute(): string
    {
        return [
            self::TYPE_FTW => 'First Time Work (FTW)',
            self::TYPE_DFIT => 'Evaluasi D-Fit',
            self::TYPE_FATIGUE_CHECK => 'Fatigue Check',
            self::TYPE_WAKEUP_CALL => 'Wake Up Call',
            self::TYPE_SAGA => 'Inspeksi SAGA',
            self::TYPE_SIDAK => 'Sidak Napping'
        ][$this->activity_type] ?? 'Unknown Activity';
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getDeadlineInCarbon()->format('d M Y H:i') . ' WIB';
    }

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->result_path);
    }

    public function getShouldSendNotificationAttribute(): bool
    {
        return $this->shouldSendNotification();
    }

    // ==================== RELATIONSHIPS ====================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

     public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }


    // ==================== QUERY SCOPES ====================
    public function scopePendingNotifications($query)
    {
        return $query->with('user')
            ->whereNull('result_path')
            ->whereHas('user', function($q) {
                $q->whereNotNull('no_telp');
            });
    }
}
