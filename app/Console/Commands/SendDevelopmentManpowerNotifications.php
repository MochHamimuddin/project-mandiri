<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\DevelopmentManpower;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDevelopmentManpowerNotifications extends Command
{
    protected $signature = 'notifications:development';
    protected $description = 'Kirim notifikasi WA untuk aktivitas development manpower yang belum didokumentasikan';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentMonth = $now->format('Y-m-d');
    
        $overdueActivities = $this->getOverdueActivities($now);
    
        if ($overdueActivities->isEmpty()) {
            $this->info('Tidak ada aktivitas development manpower yang melewati deadline bulan ini.');
            return;
        }

        foreach ($overdueActivities as $activityType => $deadlineTime) {
            Log::debug("Memproses aktivitas development manpower", [
                'activityType' => $activityType,
                'deadlineTime' => $deadlineTime,
                'now' => $now,
                'currentMonth' => $currentMonth,
            ]);

            $this->processActivity($activityType, $now, $currentMonth);
        }
    }

    protected function getOverdueActivities(Carbon $now)
    {
        return collect(DevelopmentManpower::$monthlyDeadlines)
            ->filter(function ($deadlineTime) use ($now) {
                $deadline = $now->copy()
                    ->setDay(17) // Deadline tanggal 17
                    ->setTime(...explode(':', $deadlineTime));
                
                return $now->greaterThanOrEqualTo($deadline);
            });
    }

    protected function processActivity($activityType, $now, $currentMonth)
    {
        $activities = User::whereNotNull('no_telp')
        ->whereDoesntHave('developmentManpower', function($query) use ($activityType, $now) {
            $query->where('kategori_aktivitas', $activityType)
                //   ->whereDate('created_at', $now)
                  ->whereNotNull('foto_aktivitas')
                ->orWhereNull('dokumen_1');
        })
        ->get();

        foreach ($activities as $activity) {
            Log::debug("Mengirim notifikasi ke pengawas", [
                'activity_id' => $activity->id,
                'activityType' => $activityType,
                'pengawas' => $activity->pengawas,
                'now' => $now,
                'currentMonth' => $currentMonth,
            ]);
        
            $this->sendSupervisorNotification($activity, $activityType, $now);
        }
    }

    // protected function sendSupervisorNotification($activity, $activityType, $now)
    // {

    //     foreach ($activity as $user) {
    //         $userId = $user->id;
    //         $cacheKey = "fire_notif_{$userId}_{$activityType}_{$now}";
        
    //         // Lewati jika notifikasi sudah dikirim (ada di cache)
    //         if (cache()->has($cacheKey)) {
    //             Log::debug("Lewati user {$userId}, notifikasi sudah dikirim sebelumnya.");
    //             continue;
    //         }
        
    //         // Logging untuk debug
    //         Log::debug('Generate cache key', [
    //             'user_id' => $userId,
    //             'cache_key' => $cacheKey,
    //         ]);
        
    //         $activityName = DevelopmentPower::$typeLabels[$activityType] ?? $activityType;
    //         $message = $this->formatMessage($activityName, $deadlineTime);
        
    //         if ($this->sendWhatsApp($user->no_telp, $message)) {
    //             cache()->put($cacheKey, true, now()->addDay()); // simpan supaya tidak kirim ulang
    //             $this->info("[BERHASIL] Notifikasi terkirim ke {$user->no_telp} untuk {$activityName}");
    //         } else {
    //             $this->error("[GAGAL] Gagal mengirim ke {$user->no_telp} untuk {$activityName}");
    //         }
    //     }
    // }

    protected function sendSupervisorNotification($activity, $activityType, $now)
    {
        $cacheKey = "fatigue_notif_{$activity->id}_{$activityType}_{$now}";

        // 4. Skip jika sudah pernah dikirim hari ini
        if (cache()->has($cacheKey)) {
            return;
        }

        $activityName = DevelopmentManpower::$typeLabels[$activityType] ?? $activityType;
        $message = $this->formatMessage($activityName, $now);

        if ($this->sendWhatsApp($activity->no_telp, $message)) {
            cache()->put($cacheKey, true, now()->addDay());
            $this->info("[BERHASIL] Notifikasi terkirim ke {$activity->no_telp} untuk {$activityName}");
        } else {
            $this->error("[GAGAL] Gagal mengirim ke {$activity->no_telp} untuk {$activityName}");
        }
    }

    // protected function sendSupervisorNotification($activity, $activityType, $now)
    // {
    //     // Pastikan activity valid dan memiliki pengawas
    //     if (!$activity || !$activity->pengawas) {
    //         Log::error('Activity atau pengawas tidak valid', ['activity' => $activity]);
    //         return;
    //     }
    
    //     $userId = $activity->id;
    //     $cacheKey = "dev_manpower_notif_{$userId}_{$activityType}_{$now->format('Y-m')}";
    
    //     // Lewati jika notifikasi sudah dikirim bulan ini
    //     if (cache()->has($cacheKey)) {
    //         Log::debug("Lewati pengawas {$userId}, notifikasi sudah dikirim bulan ini.");
    //         return;
    //     }
    
    //     // Logging untuk debug
    //     Log::debug('Generate cache key', [
    //         'activity_id' => $activity->id,
    //         'user_id' => $userId,
    //         'cache_key' => $cacheKey,
    //     ]);
    
    //     $activityName = $activity->kategori_aktivitas_formatted;
    //     $deadlineTime = $activity->deadline_time_wib;
    //     $message = $this->formatMessage($activityName, $deadlineTime);
    
    //     if ($this->sendWhatsApp($activity->pengawas->no_telp, $message)) {
    //         cache()->put($cacheKey, true, now()->addMonth()); // simpan sampai bulan depan
    //         $this->info("[BERHASIL] Notifikasi terkirim ke {$activity->pengawas->no_telp} untuk {$activityName}");
            
    //         Log::info('Notifikasi development manpower terkirim', [
    //             'activity_id' => $activity->id,
    //             'pengawas_id' => $userId,
    //             'phone' => $activity->pengawas->no_telp,
    //             'activity_type' => $activityType,
    //             'timestamp' => now(),
    //         ]);
    //     } else {
    //         $this->error("[GAGAL] Gagal mengirim ke {$activity->pengawas->no_telp} untuk {$activityName}");
            
    //         Log::error('Gagal mengirim notifikasi development manpower', [
    //             'activity_id' => $activity->id,
    //             'pengawas_id' => $userId,
    //             'phone' => $activity->pengawas->no_telp,
    //             'activity_type' => $activityType,
    //             'timestamp' => now(),
    //         ]);
    //     }
    // }


    protected function formatMessage($activityName, $deadlineTime)
    {
        return "📢 *Pengingat Dokumentasi Aktivitas* 📢\n\n"
            . "Aktivitas *{$activityName}* belum didokumentasikan untuk bulan ini.\n"
            . "Deadline: *{$deadlineTime}*\n\n"
            . "Segera upload foto dan dokumen pendukung melalui sistem.";
    }

    private function sendWhatsApp($phone, $message): bool
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
    
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.key'),
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target' => $formattedPhone,
                'message' => $message,
                'countryCode' => '62',
            ]);
    
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error WA API untuk development manpower: " . $e->getMessage());
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