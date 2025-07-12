<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{
    public function sendDeadlineReminder(string $phone, string $activityName, string $deadline): bool
    {
        $message = $this->formatMessage($activityName, $deadline);
        return $this->sendMessage($phone, $message);
    }

    private function formatMessage(string $activityName, string $deadline): string
    {
        return <<<MSG
📢 *PENGINGAT DEADLINE* 📢

Aktivitas: *{$activityName}*
Deadline: *{$deadline}*

Segera upload dokumen sebelum waktu habis!
MSG;
    }

    private function sendMessage(string $phone, string $message): bool
    {
        $formattedPhone = $this->formatPhone($phone);
        if (!$formattedPhone) {
            Log::error('Format nomor tidak valid', ['phone' => $phone]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.key'),
            ])->timeout(15)->post(config('services.fonnte.url').'/send', [
                'target' => $formattedPhone,
                'message' => $message,
                'countryCode' => '62'
            ]);

            if ($response->successful()) {
                Log::info('Pesan terkirim', [
                    'to' => $formattedPhone,
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('Gagal mengirim pesan', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Error WhatsApp API: '.$e->getMessage());
            return false;
        }
    }

    private function formatPhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);  // 0812 -> 62812
        }

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);  // +62812 -> 62812
        }

        if (!str_starts_with($phone, '62')) {
            return '62'.$phone;  // 812 -> 62812
        }

        return $phone;
    }
}
