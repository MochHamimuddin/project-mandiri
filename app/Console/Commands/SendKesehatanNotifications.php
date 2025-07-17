<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ProgramKerjaKesehatan;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendKesehatanNotifications extends Command
{
    protected $signature = 'notifications:kesehatan';
    protected $description = 'Kirim notifikasi WA untuk program kesehatan yang belum diupload minggu ini';

    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');
        $currentWeek = $now->format('Y-m-d'); // Format tahun dan minggu (ex: 2023-52)

        // 1. Dapatkan semua program yang sudah lewat deadline minggu ini
        $overduePrograms = $this->getOverduePrograms($now);
        Log::debug("Program yang terlewat: {$overduePrograms}");

        if ($overduePrograms->isEmpty()) {
            $this->info('Tidak ada program yang melewati deadline minggu ini.');
            return;
        }

        // 2. Untuk setiap program yang overdue, cari pengawas yang belum mengisi
        foreach ($overduePrograms as $programType => $deadlineTime) {
            $this->processProgram($programType, $now, $currentWeek);
        }
    }

    protected function getOverduePrograms(Carbon $now)
    {
        return collect(ProgramKerjaKesehatan::$weeklyDeadlines)
            ->filter(function ($deadlineTime) use ($now) {
                // Deadline adalah Sabtu terdekat dengan jam yang ditentukan
                $deadlineThisWeek = $now->copy()
                    // ->next(Carbon::THURSDAY)
                    ->month(7)->day(17)
                        // ->next(Carbon::THURSDAY) // Sabtu berikutnya
                    // ->setTime($hours, $minutes, 0);
                    ->setTime(...explode(':', $deadlineTime));
                
                return $now->greaterThanOrEqualTo($deadlineThisWeek);
            });
    }

    protected function processProgram($programType, $now, $currentWeek)
    {
        // Cari pengawas yang memiliki program belum lengkap
            $activities = User::whereNotNull('no_telp')
            ->whereDoesntHave('programKesehatan', function($query) use ($programType, $now) {
                $query->where('jenis_program', $programType)
                    //   ->whereDate('created_at', $now)
                      ->whereNotNull('foto_path')
                    ->orWhereNull('dokumen_path');
            })
            ->get();

        foreach ($activities as $pengawas) {
            $this->sendPengawasNotification($activities, $programType, $now, $currentWeek);
        }
    }

    protected function sendPengawasNotification($activities, $programType, $deadlineTime, $now)
    {
        // $cacheKey = "kesehatan_notif_{$pengawas->id}_{$programType}_{$currentWeek}";

        // // Skip jika sudah pernah dikirim minggu ini
        // if (cache()->has($cacheKey)) {
        //     return;
        // }

        // $programName = ProgramKerjaKesehatan::$typeLabels[$programType] ?? $programType;
        // $message = $this->formatMessage($programName, $deadlineTime);

        // if ($this->sendWhatsApp($pengawas->no_telp, $message)) {
        //     cache()->put($cacheKey, true, now()->addWeek()); // Cache selama 1 minggu
        //     $this->info("[BERHASIL] Notifikasi terkirim ke {$pengawas->no_telp} untuk {$programName}");
        //     Log::channel('kesehatan_notifications')->info("Notification sent", [
        //         'pengawas' => $pengawas->id,
        //         'program' => $programType,
        //         'phone' => $pengawas->no_telp
        //     ]);
        // } else {
        //     $this->error("[GAGAL] Gagal mengirim ke {$pengawas->no_telp} untuk {$programName}");
        //     Log::channel('kesehatan_notifications')->error("Failed to send notification", [
        //         'pengawas' => $pengawas->id,
        //         'program' => $programType,
        //         'phone' => $pengawas->no_telp
        //     ]);
        // }

        foreach ($activities as $user) {
            $userId = $user->id;
            $cacheKey = "fire_notif_{$userId}_{$programType}_{$now}";
        
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
        
            $activityName = ProgramKerjaKesehatan::$typeLabels[$programType] ?? $programType;
            $message = $this->formatMessage($activityName, $deadlineTime);
        
            if ($this->sendWhatsApp($user->no_telp, $message)) {
                cache()->put($cacheKey, true, now()->addDay()); // simpan supaya tidak kirim ulang
                $this->info("[BERHASIL] Notifikasi terkirim ke {$user->no_telp} untuk {$activityName}");
            } else {
                $this->error("[GAGAL] Gagal mengirim ke {$user->no_telp} untuk {$activityName}");
            }
        }

    }

    protected function formatMessage($programName, $deadlineTime)
    {
        return "🏥 *Pengingat Program Kesehatan* 🏥\n\n"
            . "Anda belum melengkapi dokumen untuk:\n*{$programName}*\n"
            . "Deadline: *Sabtu {$deadlineTime} WIB*\n\n"
            . "Segera upload dokumen melalui aplikasi!";
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
            Log::channel('kesehatan_notifications')->error("WhatsApp API Error: " . $e->getMessage());
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