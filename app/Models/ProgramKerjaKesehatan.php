<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsappNotificationService;

class ProgramKerjaKesehatan extends Model
{
    use HasFactory, SoftDeletes;

    // Konstanta untuk jenis program
    const MCU_TAHUNAN = 'MCU_TAHUNAN';
    const PENYAKIT_KRONIS = 'PENYAKIT_KRONIS';

    // Deadline untuk setiap jenis program (Sabtu jam 8 dan 9 pagi)
    public static $weeklyDeadlines = [
        self::MCU_TAHUNAN => '01:00', // Sabtu jam 8 pagi
        self::PENYAKIT_KRONIS => '01:20' // Sabtu jam 9 pagi
    ];

    protected $table = 'program_kerja_kesehatan';

    protected $fillable = [
        'jenis_program',
        'foto_path',
        'dokumen_path',
        'deskripsi',
        'pengawas_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'tanggal_upload'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    protected $appends = [
        'jenis_program_label',
        'deadline_time_wib',
        'is_uploaded',
        'should_send_notification'
    ];

    // ==================== CORE METHODS ====================

    /**
     * Check if notification should be sent
     */
    public function shouldSendNotification(): bool
    {
        // Skip jika sudah upload
        if ($this->is_uploaded) {
            return false;
        }

        // Skip jika pengawas tidak ada nomor WA
        if (empty($this->pengawas->no_telp)) {
            return false;
        }

        $now = now('Asia/Jakarta');
        $nextDeadline = $this->getNextDeadline();

        // Hanya kirim jika sekarang sudah lewat deadline minggu ini
        if ($now->lessThan($nextDeadline)) {
            return false;
        }

        // Cek apakah sudah pernah dikirim minggu ini
        $cacheKey = $this->getNotificationCacheKey();
        return !Cache::has($cacheKey);
    }

    /**
     * Get cache key for notification tracking (weekly basis)
     */
    protected function getNotificationCacheKey(): string
    {
        return sprintf('program_kesehatan_notif_%s_%s_%s',
            $this->pengawas_id,
            $this->jenis_program,
            now('Asia/Jakarta')->format('Y-W') // Tahun dan minggu
        );
    }

    /**
     * Send notification via WhatsApp
     */
    public function sendNotification(): bool
    {
        if (!$this->shouldSendNotification()) {
            return false;
        }

        $service = new WhatsappNotificationService();
        $message = $this->getNotificationMessage();

        if ($service->sendMessage($this->pengawas->no_telp, $message)) {
            Cache::put(
                $this->getNotificationCacheKey(),
                true,
                now()->addWeek() // Cache untuk seminggu
            );
            return true;
        }

        return false;
    }

    /**
     * Get notification message
     */
    protected function getNotificationMessage(): string
    {
        return sprintf(
            "⚠️ **Pengingat Program Kesehatan!** Dokumen untuk program %s belum diupload. Deadline minggu ini: %s WIB",
            $this->jenis_program_label,
            $this->getNextDeadline()->format('H:i')
        );
    }

    /**
     * Get the next Saturday deadline for this program type
     */
    protected function getNextDeadline(): Carbon
    {
        $deadlineTime = self::$weeklyDeadlines[$this->jenis_program] ?? '06:00';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::now('Asia/Jakarta')
        ->month(7)->day(17)

            // ->next(Carbon::THURSDAY) // Sabtu berikutnya
        ->setTime($hours, $minutes, 0);
    }

    // ==================== ACCESSORS ====================
    public function getJenisProgramLabelAttribute(): string
    {
        return [
            self::MCU_TAHUNAN => 'MCU Tahunan',
            self::PENYAKIT_KRONIS => 'Penyakit Kronis'
        ][$this->jenis_program] ?? 'Unknown Program';
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getNextDeadline()->format('d M Y H:i') . ' WIB';
    }

    public function getIsUploadedAttribute(): bool
    {
        return !empty($this->foto_path) || !empty($this->dokumen_path);
    }

    public function getShouldSendNotificationAttribute(): bool
    {
        return $this->shouldSendNotification();
    }

    // ==================== RELATIONSHIPS ====================
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== QUERY SCOPES ====================
    public function scopeMcuTahunan($query)
    {
        return $query->where('jenis_program', self::MCU_TAHUNAN);
    }

    public function scopePenyakitKronis($query)
    {
        return $query->where('jenis_program', self::PENYAKIT_KRONIS);
    }

    /**
     * Scope untuk program yang perlu notifikasi
     */
    public function scopePendingNotifications($query)
    {
        return $query->with('pengawas')
            ->where(function($q) {
                $q->whereNull('foto_path')
                  ->orWhereNull('dokumen_path');
            })
            ->whereHas('pengawas', function($q) {
                $q->whereNotNull('no_telp');
            });
    }

    /**
     * Scope untuk program yang sudah lewat deadline
     */
    public function scopeDueForNotification($query)
    {
        $now = now('Asia/Jakarta');

        return $query->whereHas('pengawas', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->where(function($q) {
                $q->whereNull('foto_path')
                  ->orWhereNull('dokumen_path');
            })
            ->where(function($q) use ($now) {
                foreach (self::$weeklyDeadlines as $type => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadline = Carbon::now('Asia/Jakarta')
                        ->next(Carbon::SATURDAY)
                        ->setTime($hours, $minutes, 0);
                    
                    if ($now->greaterThanOrEqualTo($deadline)) {
                        $q->orWhere('jenis_program', $type);
                    }
                }
            });
    }

    /**
     * Daftar opsi jenis program untuk form select
     */
    public static function getJenisProgramOptions()
    {
        return [
            self::MCU_TAHUNAN => 'MCU Tahunan',
            self::PENYAKIT_KRONIS => 'Penyakit Kronis',
        ];
    }
}