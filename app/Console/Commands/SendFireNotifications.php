<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\FirePreventiveManagement;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFireNotifications extends Command
{
    protected $signature = 'notifications:fire';
    protected $description = 'Kirim notifikasi WA untuk aktivitas fatigue yang belum diupload hari ini';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentMonth = $now->format('Y-m-d');
    
        $overdueActivities = $this->getOverdueActivities($now);
    
        if ($overdueActivities->isEmpty()) {
            $this->info('Tidak ada aktivitas yang melewati deadline bulan ini.');
            return;
        }

        foreach ($overdueActivities as $activityType => $deadlineTime) {
            Log::debug("Processing activity", [
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
        return collect(FirePreventiveManagement::$monthlyDeadlines)
            ->filter(function ($deadlineTime) use ($now) {
                $deadline = $now->copy()
                    ->setDay(16)
                    ->setTime(...explode(':', $deadlineTime));
                
                return $now->greaterThanOrEqualTo($deadline);
            });
    }

    protected function processActivity($activityType, $now, $currentMonth)
    {

        $activities = User::whereNotNull('no_telp')
        ->whereDoesntHave('firePreventive', function($query) use ($activityType, $now) {
            $query->where('activity_type', $activityType)
                //   ->whereDate('created_at', $now)
                  ->whereNotNull('foto_path')
                ->orWhereNull('form_fpp_path');
        })
        ->get();

        
        foreach ($activities as $index => $activity) {
            Log::debug("Sending supervisor notification", [
                'index' => $index,
                'activityType' => $activityType,
                'activity' => $activity,
                'now' => $now,
                'currentMonth' => $currentMonth,
            ]);
        
            $this->sendSupervisorNotification($activities, $activityType, $now, $currentMonth);
        }
        
    }

    protected function sendSupervisorNotification($activities, $activityType, $deadlineTime, $now)
    {

        foreach ($activities as $user) {
            $userId = $user->id;
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
        
            $activityName = FirePreventiveManagement::$typeLabels[$activityType] ?? $activityType;
            $message = $this->formatMessage($activityName, $deadlineTime);
        
            if ($this->sendWhatsApp($user->no_telp, $message)) {
                cache()->put($cacheKey, true, now()->addDay()); // simpan supaya tidak kirim ulang
                $this->info("[BERHASIL] Notifikasi terkirim ke {$user->no_telp} untuk {$activityName}");
            } else {
                $this->error("[GAGAL] Gagal mengirim ke {$user->no_telp} untuk {$activityName}");
            }
        }
    }
        

    protected function formatMessage($activityName, $deadlineTime)
    {
        return "📢 *Pengingat Aktivitas* 📢\n\n"
            . "Anda belum mengupload: *{$activityName}*\n"
            . "Deadline: *{$deadlineTime} WIB*\n\n"
            . "Segera selesaikan!";
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