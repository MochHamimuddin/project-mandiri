<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InspeksiKendaraan;
use Illuminate\Support\Facades\Log;

class SendInspeksiNotifications extends Command
{
        protected $signature = 'send:inspeksi-notifications
                           {--test-mode : Force send notifications regardless of timing}
                           {--ignore-cache : Bypass notification cooldown}';

    protected $description = 'Send WhatsApp notifications for pending vehicle inspections';

    public function handle()
    {
        $this->showStartupHeader();
        $this->checkEnvironment();

        // Get pending inspections where document not uploaded
        $query = InspeksiKendaraan::whereNull('path_dokumen')
            ->with(['pengawas', 'mitra']);

        $pendingCount = $query->count();
        $this->line("🔍 Found <fg=cyan>{$pendingCount}</> pending inspections");

        if ($pendingCount === 0) {
            $this->line("<fg=yellow>No pending inspections found</>");
            $this->logNoPendingInspections();
            return 0;
        }

        $this->line("⏳ Processing inspections...");
        $this->newLine();

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        $query->chunk(100, function ($inspections) use (&$successCount, &$failedCount, &$skippedCount) {
            foreach ($inspections as $inspeksi) {
                $result = $this->processInspection($inspeksi);

                if ($result === 'sent') $successCount++;
                elseif ($result === 'failed') $failedCount++;
                else $skippedCount++;
            }
        });

        $this->showSummary($successCount, $failedCount, $skippedCount);
        return 0;
    }


    protected function processInspection($inspeksi): string
    {
        $this->line("<fg=white>Processing Inspection ID:</> <fg=cyan>{$inspeksi->id}</>");
        $this->line("- Type: <fg=cyan>{$inspeksi->jenis_inspeksi_label}</>");
        $this->line("- Supervisor: <fg=cyan>{$inspeksi->pengawas->nama_lengkap}</>");
        $this->line("- Phone: <fg=cyan>{$inspeksi->pengawas->no_telp}</>");
        $this->line("- Deadline: <fg=cyan>{$inspeksi->deadline_time_wib}</>");

        // Temporary override for testing
        if ($this->option('test-mode')) {
            $inspeksi->shouldSendNotification = fn() => true;
        }

        // Temporary cache bypass for testing
        if ($this->option('ignore-cache')) {
            cache()->forget("inspeksi_{$inspeksi->id}_last_notification");
        }

        try {
            if ($inspeksi->sendNotification()) {
                $this->line("<fg=green>✓ Notification sent successfully</>");
                Log::info("Notification sent for inspection {$inspeksi->id}");
                return 'sent';
            }

            $this->line("<fg=yellow>✗ Notification not sent (check logs)</>");
            return 'skipped';

        } catch (\Exception $e) {
            $this->line("<fg=red>✗ Error sending notification:</> {$e->getMessage()}");
            Log::error("Failed to send notification for inspection {$inspeksi->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'failed';
        }
    }

    protected function showStartupHeader(): void
    {
        $this->line("<fg=magenta>=====================================</>");
        $this->line("<fg=magenta>  VEHICLE INSPECTION NOTIFICATION SENDER</>");
        $this->line("<fg=magenta>=====================================</>");
        $this->line("Current time: <fg=cyan>" . now('Asia/Jakarta')->format('Y-m-d H:i:s') . " WIB</>");
        $this->newLine();
    }

    protected function checkEnvironment(): void
    {
        if (app()->environment('local') && !$this->option('test-mode')) {
            $this->line("<fg=yellow>WARNING:</> Running in local environment without --test-mode");
            $this->line("Notifications will only send if conditions are met");
            $this->newLine();
        }
    }

    protected function showSummary(int $success, int $failed, int $skipped): void
    {
        $this->newLine();
        $this->line("<fg=magenta>========== SUMMARY ==========</>");
        $this->line("<fg=green>✓ Successfully sent:</> {$success}");
        $this->line("<fg=red>✗ Failed to send:</> {$failed}");
        $this->line("<fg=yellow>↻ Skipped:</> {$skipped}");
        $this->line("<fg=magenta>============================</>");

        Log::info("Inspection notifications summary", [
            'success' => $success,
            'failed' => $failed,
            'skipped' => $skipped
        ]);
    }

    protected function logNoPendingInspections(): void
    {
        Log::info("No pending inspections found matching criteria", [
            'conditions' => [
                'path_dokumen_null' => true,
                'has_supervisor_with_phone' => true
            ]
        ]);
    }
}
