<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\FatigueActivity;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFatigueNotifications extends Command
{
    protected $signature = 'notifications:fatigue';
    protected $description = 'Kirim notifikasi WA untuk aktivitas fatigue yang belum diupload hari ini';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->format('Y-m-d');

        // 1. Dapatkan semua aktivitas yang sudah lewat deadline hari ini
        $overdueActivities = $this->getOverdueActivities($now);

        if ($overdueActivities->isEmpty()) {
            $this->info('Tidak ada aktivitas yang melewati deadline hari ini.');
            return;
        }

        // 2. Untuk setiap aktivitas yang overdue, cari user yang belum mengisi
        foreach ($overdueActivities as $activityType => $deadlineTime) {
            $this->processActivity($activityType, $deadlineTime, $today);
        }
    }

    protected function getOverdueActivities(Carbon $now)
    {
        return collect(FatigueActivity::$dailyDeadlines)
            ->filter(function ($deadlineTime) use ($now) {
                $deadlineToday = $now->copy()->setTime(...explode(':', $deadlineTime));
                return $now->greaterThanOrEqualTo($deadlineToday);
            });
    }

    protected function processActivity($activityType, $deadlineTime, $today)
    {
        $users = User::whereNotNull('no_telp')
            ->whereDoesntHave('fatigueActivities', function($query) use ($activityType, $today) {
                $query->where('activity_type', $activityType)
                      ->whereDate('created_at', $today)
                      ->whereNotNull('photo_path');
            })
            ->get();

        foreach ($users as $user) {
            $this->sendUserNotification($user, $activityType, $deadlineTime, $today);
        }
    }

    protected function sendUserNotification($user, $activityType, $deadlineTime, $today)
    {
        $cacheKey = "fatigue_notif_{$user->id}_{$activityType}_{$today}";

        // 4. Skip jika sudah pernah dikirim hari ini
        // if (cache()->has($cacheKey)) {
        //     return;
        // }

        $activityName = FatigueActivity::$typeLabels[$activityType] ?? $activityType;
        $message = $this->formatMessage($activityName, $deadlineTime);

        if ($this->sendWhatsApp($user->no_telp, $message)) {
            cache()->put($cacheKey, true, now()->addDay());
            $this->info("[BERHASIL] Notifikasi terkirim ke {$user->no_telp} untuk {$activityName}");
        } else {
            $this->error("[GAGAL] Gagal mengirim ke {$user->no_telp} untuk {$activityName}");
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
