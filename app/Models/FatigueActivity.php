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

    public static $dailyDeadlines = [
        self::TYPE_FTW => '04:12',
        self::TYPE_DFIT => '04:54',
        self::TYPE_FATIGUE_CHECK => '04:57',
        self::TYPE_WAKEUP_CALL => '04:58',
        self::TYPE_SAGA => '05:00',
        self::TYPE_SIDAK => '05:03'
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

    public function shouldSendNotification(): bool
    {

        if ($this->is_uploaded) {
            return false;
        }

        if (empty($this->user->no_telp)) {
            return false;
        }

        if ($this->userHasCompleteActivityToday()) {
            return false;
        }

        $now = now('Asia/Jakarta');
        $todayDeadline = $this->getTodayDeadline();

        if ($now->lessThan($todayDeadline)) {
            return false;
        }

        $cacheKey = $this->getNotificationCacheKey();
        return !Cache::has($cacheKey);
    }


    protected function userHasCompleteActivityToday(): bool
    {
        return static::where('user_id', $this->user_id)
            ->where('activity_type', $this->activity_type)
            ->whereDate('created_at', now('Asia/Jakarta')->toDateString())
            ->whereNotNull('photo_path')
            ->exists();
    }

    protected function getNotificationCacheKey(): string
    {
        return sprintf('fatigue_notif_%s_%s_%s',
            $this->user_id,
            $this->activity_type,
            now('Asia/Jakarta')->format('Y-m-d')
        );
    }

    public function sendNotification(): bool
    {
        if (!$this->shouldSendNotification()) {
            return false;
        }

        $service = new WhatsappNotificationService();
        $message = $this->getNotificationMessage();

        if ($service->sendMessage($this->user->no_telp, $message)) {
            Cache::put(
                $this->getNotificationCacheKey(),
                true,
                now()->addDay()
            );
            return true;
        }

        return false;
    }

    protected function getNotificationMessage(): string
    {
        return sprintf(
            "⚠️ **Peringatan!** Aktivitas %s belum diupload. Deadline: %s WIB",
            $this->activity_type_label,
            $this->getTodayDeadline()->format('H:i')
        );
    }

    protected function getTodayDeadline(): Carbon
    {
        $deadlineTime = self::$dailyDeadlines[$this->activity_type] ?? '23:59';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::now('Asia/Jakarta')
            ->setTime($hours, $minutes, 0);
    }

    // ==================== ACCESSORS ====================
    public function getActivityTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->activity_type] ?? 'Unknown Activity';
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getTodayDeadline()->format('d M Y H:i') . ' WIB';
    }

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->photo_path);
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

    // ==================== QUERY SCOPES ====================
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopePendingNotifications($query)
    {
        return $query->with('user')
            ->whereNull('photo_path')
            ->whereHas('user', function($q) {
                $q->whereNotNull('no_telp');
            });
    }

    public function scopeDueForNotification($query)
    {
        $now = now('Asia/Jakarta');

        return $query->whereHas('user', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->whereNull('photo_path')
            ->where(function($q) use ($now) {
                foreach (self::$dailyDeadlines as $type => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadlineToday = Carbon::today('Asia/Jakarta')->setTime($hours, $minutes, 0);
                    if ($now->greaterThanOrEqualTo($deadlineToday)) {
                        $q->orWhere('activity_type', $type);
                    }
                }
            });
    }

    /**
     * Scope to find users who need notifications
     */
    public function scopeNeedNotification($query)
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        return $query->whereNull('photo_path')
            ->whereHas('user', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->whereDoesntHave('user.fatigueActivities', function($q) use ($today) {
                $q->whereDate('created_at', $today)
                    ->whereNotNull('photo_path');
            })
            ->where(function($q) use ($now) {
                foreach (self::$dailyDeadlines as $type => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadlineToday = Carbon::today('Asia/Jakarta')->setTime($hours, $minutes, 0);
                    if ($now->greaterThanOrEqualTo($deadlineToday)) {
                        $q->orWhere('activity_type', $type);
                    }
                }
            });
    }
}
