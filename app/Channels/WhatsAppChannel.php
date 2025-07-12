<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function send($notifiable, Notification $notification)
    {
        try {
            // Verify notification has required method
            if (!method_exists($notification, 'toFonnte')) {
                throw new \Exception("Notification must implement toFonnte() method");
            }

            // Get message data
            $messageData = $notification->toFonnte($notifiable);

            // Validate required fields
            if (empty($messageData['to'])) {
                throw new \Exception("Recipient phone number is required");
            }

            if (empty($messageData['message'])) {
                throw new \Exception("Message content is required");
            }

            // Format phone number if needed
            $phoneNumber = $this->formatPhoneNumber($messageData['to']);

            // Log the sending attempt
            Log::debug('Attempting to send WhatsApp notification', [
                'to' => $phoneNumber,
                'message_length' => strlen($messageData['message']),
                'options' => $messageData['options'] ?? []
            ]);

            // Send message via Fonnte service
            $result = $this->fonnteService->sendMessage(
                $phoneNumber,
                $messageData['message'],
                $messageData['options'] ?? []
            );

            // Debug log the raw response
            Log::debug('Fonnte API raw response', ['response' => $result]);

            // Validate response structure
            if (!is_array($result)) {
                throw new \Exception("Invalid response format from Fonnte service - expected array, got " . gettype($result));
            }

            if (!isset($result['success'])) {
                throw new \Exception("Missing success indicator in Fonnte response");
            }

            // Handle failed response
            if (!$result['success']) {
                $errorMsg = $result['error'] ?? $result['message'] ?? 'Unknown error';
                throw new \Exception("Fonnte API error: " . $errorMsg);
            }

            // Log successful delivery
            Log::info('WhatsApp notification delivered successfully', [
                'to' => $phoneNumber,
                'response' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('WhatsAppChannel delivery failed', [
                'user_id' => $notifiable->id ?? null,
                'phone' => $messageData['to'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification' => get_class($notification)
            ]);

            throw $e; // Re-throw to allow Laravel to handle the failure
        }
    }

    protected function formatPhoneNumber($phone)
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
