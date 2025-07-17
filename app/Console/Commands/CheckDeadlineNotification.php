<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataLaporan;
use Carbon\Carbon;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Support\Facades\Log;
use App\Events\DeadlineCheckUpdate;

class CheckDeadlineNotification extends Command
{
    // protected $signature = 'check:deadline';

    protected $signature = 'check:deadline {type : fire|fatigue|lingkungan|kesehatan}';
    protected $description = 'Check deadline and send WhatsApp notifications with real-time updates';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type === 'fatigue' || $type === 'fire' || $type === 'lingkungan' || $type === 'kesehatan'){
        // Broadcast start event
        event(new DeadlineCheckUpdate(
            'Memulai pengecekan deadline',
            'start',
            0,
            0,
            now()->format('Y-m-d H:i:s')
        ));

        $now = Carbon::now('Asia/Jakarta');
        Log::info("Starting deadline check at {$now->format('Y-m-d H:i:s')}");

        $reports = DataLaporan::with(['user'])
            ->where('is_upload', 0)
            ->where('deadline_time', '<=', $now)
            ->whereNull('deleted_at')
            ->get();

        $totalReports = $reports->count();
        $successCount = 0;
        $failedCount = 0;

        // Broadcast total reports found
        event(new DeadlineCheckUpdate(
            "Menemukan {$totalReports} laporan yang perlu diproses",
            'progress',
            0,
            $totalReports,
            now()->format('Y-m-d H:i:s')
        ));

        foreach ($reports as $index => $report) {
            try {
                if (!$report->user || !$report->user->no_telp) {
                    $message = "Melewati laporan {$report->id} - tidak ada pengguna atau nomor telepon";

                    event(new DeadlineCheckUpdate(
                        $message,
                        'warning',
                        $index + 1,
                        $totalReports,
                        now()->format('Y-m-d H:i:s')
                    ));

                    $this->warn($message);
                    continue;
                }

                $message = "Memproses laporan #{$report->id} untuk {$report->user->no_telp}";

                event(new DeadlineCheckUpdate(
                    $message,
                    'processing',
                    $index + 1,
                    $totalReports,
                    now()->format('Y-m-d H:i:s')
                ));

                $this->info($message);

                // Send notification
                $report->user->notify(new DeadlineReminderNotification($report));
                $successCount++;

                // Broadcast success
                event(new DeadlineCheckUpdate(
                    "Notifikasi terkirim ke {$report->user->no_telp}",
                    'success',
                    $index + 1,
                    $totalReports,
                    now()->format('Y-m-d H:i:s')
                ));

                sleep(1); // Delay untuk menghindari rate limit

            } catch (\Exception $e) {
                $failedCount++;
                $phoneNumber = $report->user->no_telp ?? 'unknown';
                $errorMessage = "Failed to send to {$phoneNumber}: {$e->getMessage()}";
                event(new DeadlineCheckUpdate(
                    $errorMessage,
                    'error',
                    $index + 1,
                    $totalReports,
                    now()->format('Y-m-d H:i:s')
                ));

                $this->error($errorMessage);
                Log::error($errorMessage, [
                    'report_id' => $report->id,
                    'error' => $e->getTraceAsString()
                ]);
            }
        }

        $summary = "Selesai. Berhasil: {$successCount}, Gagal: {$failedCount}";

        event(new DeadlineCheckUpdate(
            $summary,
            $failedCount > 0 ? 'completed-with-errors' : 'completed',
            $totalReports,
            $totalReports,
            now()->format('Y-m-d H:i:s')
        ));

        $this->info($summary);
        Log::info($summary);
    }

        }
    }
