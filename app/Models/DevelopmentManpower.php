<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsappNotificationService;

class DevelopmentManpower extends Model
{
    use HasFactory;

    protected $table = 'development_manpower';

    protected $fillable = [
        'kategori_aktivitas',
        'posisi',
        'foto_aktivitas',
        'dokumen_1',
        'dokumen_2',
        'deskripsi',
        'pengawas_id',
        'pelaku_korban_id',
        'saksi_id',
        'kronologi',
        'tanggal_aktivitas',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal_aktivitas' => 'date',
    ];

    // Deadline time for each activity (17th of each month at 08:00 WIB)
    public static $monthlyDeadlines = [
        'SKKP/POP For GL Mitra' => '01:00',
        'Training HRCP Mitra' => '02:00',
        'Training Additional Plant' => '03:00',
        'Review IBPR' => '04:00',
        'Review SMKP For Mitra Kerja' => '05:00',
        'Pembinaan Pelanggaran' => '06:00'
    ];

    // Kategori aktivitas yang tersedia
    const KATEGORI_AKTIVITAS = [
        'SKKP/POP For GL Mitra',
        'Training HRCP Mitra',
        'Training Additional Plant',
        'Review IBPR',
        'Review SMKP For Mitra Kerja',
        'Pembinaan Pelanggaran'
    ];

    protected $appends = [
        'kategori_aktivitas_formatted',
        'deadline_time_wib',
        'is_documented',
        'should_send_notification'
    ];

    // ==================== NOTIFICATION LOGIC ====================
    public function shouldSendNotification(): bool
    {
        // Skip jika sudah upload dokumen
        if ($this->is_documented) {
            return false;
        }

        // Skip jika pengawas tidak ada nomor WA
        if (empty($this->pengawas->no_telp)) {
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
        return sprintf('dev_manpower_notif_%s_%s_%s',
            $this->id,
            $this->kategori_aktivitas,
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

        if ($service->sendMessage($this->pengawas->no_telp, $message)) {
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
            "⚠️ **Peringatan!** Aktivitas %s belum didokumentasikan untuk bulan ini. Deadline: %s WIB",
            $this->kategori_aktivitas_formatted,
            $this->getCurrentMonthDeadline()->format('d M Y H:i')
        );
    }

    /**
     * Get current month's deadline (tanggal 17 dengan jam sesuai aktivitas)
     */
    protected function getCurrentMonthDeadline(): Carbon
    {
        $deadlineTime = self::$monthlyDeadlines[$this->kategori_aktivitas] ?? '04:00';
        [$hours, $minutes] = explode(':', $deadlineTime);

        return Carbon::now('Asia/Jakarta')
            ->setDay(17) // Tanggal 17 setiap bulan
            ->setTime($hours, $minutes, 0);
    }

    // ==================== ACCESSORS ====================
    public function getKategoriAktivitasFormattedAttribute(): string
    {
        return str_replace('_', ' ', $this->kategori_aktivitas);
    }

    public function getDeadlineTimeWibAttribute(): string
    {
        return $this->getCurrentMonthDeadline()->format('d M Y H:i') . ' WIB';
    }

    public function getIsDocumentedAttribute(): bool
    {
        return !empty($this->foto_aktivitas) || !empty($this->dokumen_1) || !empty($this->dokumen_2);
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

    public function pelakuKorban()
    {
        return $this->belongsTo(User::class, 'pelaku_korban_id');
    }

    public function saksi()
    {
        return $this->belongsTo(User::class, 'saksi_id');
    }

    // ==================== QUERY SCOPES ====================
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori_aktivitas', $kategori);
    }

    public function scopeTanggalRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_aktivitas', [$start, $end]);
    }

    public function scopeDueForNotification($query)
    {
        $now = now('Asia/Jakarta');

        return $query->whereHas('pengawas', function($q) {
                $q->whereNotNull('no_telp');
            })
            ->where(function($q) {
                $q->whereNull('foto_aktivitas')
                  ->whereNull('dokumen_1')
                  ->whereNull('dokumen_2');
            })
            ->where(function($q) use ($now) {
                foreach (self::$monthlyDeadlines as $kategori => $time) {
                    [$hours, $minutes] = explode(':', $time);
                    $deadline = Carbon::now('Asia/Jakarta')
                        ->setDay(17)
                        ->setTime($hours, $minutes, 0);
                    
                    if ($now->greaterThanOrEqualTo($deadline)) {
                        $q->orWhere('kategori_aktivitas', $kategori);
                    }
                }
            });
    }
}