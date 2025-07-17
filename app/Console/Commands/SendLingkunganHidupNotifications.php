<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ProgramLingkunganHidup;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLingkunganHidupNotifications extends Command
{
    protected $signature = 'notifications:lingkungan';
    protected $description = 'Kirim notifikasi WA untuk program lingkungan hidup yang belum diupload minggu ini';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentWeek = $now->format('Y-m-d'); // Format tahun dan nomor minggu

        // 1. Dapatkan semua aktivitas yang sudah lewat deadline minggu ini (Sabtu)
        $overdueActivities = $this->getOverdueActivities($now);

        if ($overdueActivities->isEmpty()) {
            $this->info('Tidak ada program lingkungan yang melewati deadline minggu ini.');
            return;
        }

        // 2. Untuk setiap aktivitas yang overdue, cari pelaksana yang belum mengisi
        foreach ($overdueActivities as $activityType => $deadlineTime) {
            $this->processActivity($activityType, $deadlineTime, $currentWeek);
        }
    }

    protected function getOverdueActivities(Carbon $now)
    {
        return collect(ProgramLingkunganHidup::$weeklyDeadlines)
            ->filter(function ($deadlineTime) use ($now) {
                // Deadline mingguan adalah Sabtu jam tertentu
                $deadlineThisWeek = $now->copy()
                    ->startOfWeek()      // Minggu
                    ->addDays(3)         // Jumat
                    ->setTime(...explode(':', $deadlineTime));
                
                return $now->greaterThanOrEqualTo($deadlineThisWeek);
            });
    }

    protected function processActivity($activityType, $now, $currentWeek)
    {
        $user = User::whereNotNull('no_telp')
        ->whereDoesntHave('programLingkungan', function($query) use ($activityType, $now) {
            $query->where('jenis_kegiatan', $activityType)
                //   ->whereDate('created_at', $now)
                  ->whereNotNull('upload_foto')
                ->orWhereNull('deskripsi');
        })
        ->get();

        foreach ($user as $users) {
            $this->sendUserNotification($user, $activityType, $now, $currentWeek);
        }
    }

    protected function sendUserNotification($user, $activityType, $deadlineTime, $now)
    {
        foreach ($user as $users) {
            $userId = $users->id;
            $cacheKey = "fire_notif_{$userId}_{$activityType}_{$now}";
        
            // Lewati jika notifikasi sudah dikirim (ada di cache)
            if (cache()->has($cacheKey)) {
                Log::debug("Lewati user {$userId}, notifikasi sudah dikirim sebelumnya.");
                continue;
            }
        
            // Logging untuk debug
            Log::debug('Generate cache key', [
                'user_id' => $userId,
                'cache_key' => $cacheKey,
            ]);
        
            $activityName = ProgramLingkunganHidup::$typeLabels[$activityType] ?? $activityType;
            $message = $this->formatMessage($activityName, $deadlineTime);
        
            if ($this->sendWhatsApp($users->no_telp, $message)) {
                cache()->put($cacheKey, true, now()->addDay()); // simpan supaya tidak kirim ulang
                $this->info("[BERHASIL] Notifikasi terkirim ke {$users->no_telp} untuk {$activityName}");
            } else {
                $this->error("[GAGAL] Gagal mengirim ke {$users->no_telp} untuk {$activityName}");
            }
        }

    }

    protected function formatMessage($activityName, $deadlineTime)
    {
        $deadlineDate = Carbon::now('Asia/Jakarta')
            ->startOfWeek()
            ->addDays(6) // Sabtu
            ->format('d F Y');

        return "🌿 *Pengingat Program Lingkungan* 🌿\n\n"
            . "Anda belum mengupload laporan: *{$activityName}*\n"
            . "Deadline: *{$deadlineTime} WIB, {$deadlineDate}*\n\n"
            . "Segera upload laporan Anda sebelum deadline!";
    }

    private function sendWhatsApp($phone, $message): bool
    {
        try {
            $phone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.key'),
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error WA API: " . $e->getMessage());
            return false;
        }
    }

    private function formatPhoneNumber($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }
}