<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsappNotificationService;

class ProgramLingkunganHidup extends Model
{
    use HasFactory, SoftDeletes;

    // Activity Type Constants
    const TYPE_KRIDA_AREA = 'Krida Area Office/Workshop';
    const TYPE_PENGELOLAAN = 'Pengelolaan Lingkungan Workshop';

    public static $typeLabels = [
        self::TYPE_KRIDA_AREA => 'Krida Area Office/Workshop',
        self::TYPE_PENGELOLAAN => 'Pengelolaan Lingkungan Workshop'
    ];

    public static $validActivityTypes = [
        self::TYPE_KRIDA_AREA,
        self::TYPE_PENGELOLAAN
    ];

    // Weekly Deadlines (Saturday at 8:00 and 9:00 WIB)
    public static $weeklyDeadlines = [
        self::TYPE_KRIDA_AREA => '02:00', // Sabtu jam 8:00 WIB
        self::TYPE_PENGELOLAAN => '02:30'  // Sabtu jam 9:00 WIB
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'program_lingkungan_hidup';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'jenis_kegiatan',
        'upload_foto',
        'deskripsi',
        'detail_temuan',
        'tindakan_perbaikan',
        'tanggal_kegiatan',
        'lokasi',
        'pelaksana',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'tanggal_kegiatan',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'activity_type_label',
        'deadline_time_wib',
        'is_uploaded',
        'should_send_notification'
    ];

    public function shouldSendNotification(): bool
    {
        // Skip jika sudah upload
        if ($this->is_uploaded) {
            return false;
        }

        // Skip jika pelaksana tidak ada nomor WA
        if (empty($this->pelaksanaUser->no_telp)) {
            return false;
        }

        // Skip jika sudah memiliki data aktivitas yang lengkap minggu ini
        if ($this->userHasCompleteActivityThisWeek()) {
            return false;
        }

        $now = now('Asia/Jakarta');
        $weekDeadline = $this->getThisWeekDeadline();

        // Hanya kirim jika sekarang sudah lewat deadline minggu ini
        if ($now->lessThan($weekDeadline)) {
            return false;
        }

        // Cek apakah sudah pernah dikirim minggu ini
        $cacheKey = $this->getNotificationCacheKey();
        return !Cache::has($cacheKey);
    }

    /**
     * Check if user already has complete activity data for this week
     */
    protected function userHasCompleteActivityThisWeek(): bool
    {
        $startOfWeek = now('Asia/Jakarta')->startOfWeek();
        $endOfWeek = now('Asia/Jakarta')->endOfWeek();

        return static::where('pelaksana', $this->pelaksana)
            ->where('jenis_kegiatan', $this->jenis_kegiatan)
            ->whereBetween('tanggal_kegiatan', [$startOfWeek, $endOfWeek])
            ->whereNotNull('upload_foto')
            ->exists();
    }

    /**
     * Get cache key for notification tracking
     */
    protected function getNotificationCacheKey(): string
    {
        return sprintf('env_program_notif_%s_%s_%s',
            $this->pelaksana,
            $this->jenis_kegiatan,
            now('Asia/Jakarta')->format('Y-W') // Year-WeekNumber
        );
    }

    public function sendNotification(): bool
    {
        if (!$this->shouldSendNotification()) {
            return false;
        }

        $service = new WhatsappNotificationService();
        $message = $this->getNotificationMessage();

        if ($service->sendMessage($this->pelaksanaUser->no_telp, $message)) {
            Cache::put(
                $this->getNotificationCacheKey(),
                true,
                now()->addWeek() // Cache untuk 1 minggu
            );
            return true;
        }

        return false;
    }

    protected function getNotificationMessage(): string
    {
        return sprintf(
            "⚠️ **Peringatan Mingguan!** Laporan Program Lingkungan %s belum diupload. Deadline: %s WIB",
            $this->activity_type_label,
            $this->getThisWeekDeadline()->format('H:i l, d M Y')
        );
    }

    /**
     * Get this week's deadline (Saturday at specific time)
     */
    protected function getThisWeekDeadline(): Carbon
    {
        $deadlineTime = self::$weeklyDeadlines[$this->jenis_kegiatan] ?? '23:59';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::now('Asia/Jakarta')
            ->startOfWeek() // Mulai dari Minggu
            ->addDays(3)    // Tambah 1 hari untuk sampai ke Sabtu
            ->setTime($hours, $minutes, 0);
    }

    // ==================== ACCESSORS ====================
    public function getActivityTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->jenis_kegiatan] ?? 'Unknown Activity';
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getThisWeekDeadline()->format('d M Y H:i') . ' WIB';
    }

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->upload_foto);
    }

    public function getShouldSendNotificationAttribute(): bool
    {
        return $this->shouldSendNotification();
    }

    // ==================== RELATIONSHIPS ====================
    /**
     * Relationship with User who created the record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with User who last updated the record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship with User who deleted the record
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relationship with Pelaksana User
     */
    public function pelaksanaUser()
    {
        return $this->belongsTo(User::class, 'pelaksana');
    }

    // ==================== QUERY SCOPES ====================
    /**
     * Scope for Krida Area activities
     */
    public function scopeKridaArea($query)
    {
        return $query->where('jenis_kegiatan', self::TYPE_KRIDA_AREA);
    }

    /**
     * Scope for Pengelolaan Lingkungan activities
     */
    public function scopePengelolaanLingkungan($query)
    {
        return $query->where('jenis_kegiatan', self::TYPE_PENGELOLAAN);
    }

    /**
     * Scope for pending notifications
     */
    public function scopePendingNotifications($query)
    {
        return $query->with('pelaksanaUser')
            ->whereNull('upload_foto')
            ->whereHas('pelaksanaUser', function($q) {
                $q->whereNotNull('no_telp');
            });
    }

    /**
     * Scope for activities due for notification
     */
    public function scopeDueForNotification($query)
    {
        $now = now('Asia/Jakarta');

        return $query->whereHas('pelaksanaUser', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->whereNull('upload_foto')
            ->where(function($q) use ($now) {
                foreach (self::$weeklyDeadlines as $type => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadlineThisWeek = Carbon::now('Asia/Jakarta')
                        ->startOfWeek()
                        ->addDays(6) // Sabtu
                        ->setTime($hours, $minutes, 0);
                    
                    if ($now->greaterThanOrEqualTo($deadlineThisWeek)) {
                        $q->orWhere('jenis_kegiatan', $type);
                    }
                }
            });
    }

    /**
     * Scope to find activities that need notifications
     */
    public function scopeNeedNotification($query)
    {
        $now = now('Asia/Jakarta');
        $startOfWeek = $now->startOfWeek();
        $endOfWeek = $now->endOfWeek();

        return $query->whereNull('upload_foto')
            ->whereHas('pelaksanaUser', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->whereDoesntHave('pelaksanaUser.programLingkunganHidup', function($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('tanggal_kegiatan', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('upload_foto');
            })
            ->where(function($q) use ($now) {
                foreach (self::$weeklyDeadlines as $type => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadlineThisWeek = Carbon::now('Asia/Jakarta')
                        ->startOfWeek()
                        ->addDays(6) // Sabtu
                        ->setTime($hours, $minutes, 0);
                    
                    if ($now->greaterThanOrEqualTo($deadlineThisWeek)) {
                        $q->orWhere('jenis_kegiatan', $type);
                    }
                }
            });
    }
}