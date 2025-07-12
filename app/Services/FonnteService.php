<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected $apiKey;
    protected $baseUrl;
    protected $deviceId;

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.key');
        $this->baseUrl = config('services.fonnte.url');
        $this->deviceId = config('services.fonnte.device_id');
    }

    /**
     * Send WhatsApp message via Fonnte API
     */
    public function sendMessage(string $phone, string $message, array $options = []): array
{
    try {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey, // Pastikan ini sama dengan di route
            'Accept' => 'application/json',
        ])->post($this->baseUrl.'/send', [
            'target' => $this->formatPhoneNumber($phone),
            'message' => $message,
            'countryCode' => '62',
            // Tambahkan device_id jika diperlukan
            'device_id' => $this->deviceId
        ]);

        $responseData = $response->json();

        // Debugging: Log raw request dan response
        Log::debug('Fonnte Request:', [
            'url' => $this->baseUrl.'/send',
            'payload' => [
                'target' => $phone,
                'message' => $message
            ]
        ]);
        Log::debug('Fonnte Response:', $responseData);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $responseData
            ];
        }

        return [
            'success' => false,
            'error' => $responseData['message'] ?? 'Failed to send message',
            'status' => $response->status()
        ];

    } catch (\Exception $e) {
        Log::error('Fonnte Error: '.$e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
    /**
     * Format phone number to Fonnte standard (628xxxx)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different number formats:
        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);  // 08xxx -> 628xxx
        }

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);  // +628xxx -> 628xxx
        }

        if (!str_starts_with($phone, '62')) {
            return '62'.$phone;  // 8xxx -> 628xxx
        }

        return $phone;  // 628xxx -> 628xxx (no change)
    }

    /**
     * Check device connection status
     */
    public function checkDeviceStatus(): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get($this->baseUrl.'/device');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fonnte device check failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
