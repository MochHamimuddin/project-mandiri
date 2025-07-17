<?php

namespace App\Console\Commands;

use App\Models\ProgramKerjaKesehatan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckKesehatanDeadlines extends Command
{
    protected $signature = 'kesehatan:check-deadlines';
    protected $description = 'Check program kerja kesehatan deadlines and send WhatsApp notifications';

    public function handle()
    {
        $this->info('Starting program kesehatan deadline check...');
        Log::channel('kesehatan_notifications')->info('Starting program kesehatan deadline check');

        // Ambil program yang belum lengkap dokumennya dan perlu notifikasi
        $programs = ProgramKerjaKesehatan::with(['pengawas' => function($query) {
                $query->whereNotNull('no_telp');
            }])
            ->where(function($query) {
                $query->whereNull('foto_path')
                      ->orWhereNull('dokumen_path');
            })
            ->get()
            ->filter(function($program) {
                return $program->should_send_notification;
            });

        if ($programs->isEmpty()) {
            $this->info('No notifications to send at this time.');
            Log::channel('kesehatan_notifications')->info('No notifications to send');
            return;
        }

        $this->info("Found {$programs->count()} programs requiring notifications");

        $successCount = 0;
        $failCount = 0;

        $programs->each(function ($program) use (&$successCount, &$failCount) {
            try {
                $result = $program->sendNotification();

                if ($result) {
                    $this->info("Sent to {$program->pengawas->no_telp} (Program ID: {$program->id})");
                    Log::channel('kesehatan_notifications')->info("Notification sent for Program ID: {$program->id}", [
                        'pengawas' => $program->pengawas->no_telp,
                        'jenis_program' => $program->jenis_program
                    ]);
                    $successCount++;
                } else {
                    $this->error("Failed to send for Program ID: {$program->id}");
                    Log::channel('kesehatan_notifications')->warning("Failed to send notification for Program ID: {$program->id}");
                    $failCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing Program ID {$program->id}: " . $e->getMessage());
                Log::channel('kesehatan_notifications')->error("Error processing program {$program->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $failCount++;
            }
        });

        $summary = "Processed {$programs->count()} programs: {$successCount} succeeded, {$failCount} failed";
        $this->info($summary);
        Log::channel('kesehatan_notifications')->info($summary);
    }
}