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
        if (!method_exists($notification, 'toFonnte')) {
            Log::error('Notification missing toFonnte method', [
                'notification' => get_class($notification)
            ]);
            return false;
        }

        $messageData = $notification->toFonnte($notifiable);

        if (empty($messageData['to'])) {
            Log::error('Missing phone number for WhatsApp notification', [
                'user_id' => $notifiable->id,
                'notification' => get_class($notification)
            ]);
            return false;
        }

        $result = $this->fonnteService->sendMessage(
            $messageData['to'],
            $messageData['message'],
            $messageData['options'] ?? []
        );

        if ($result['success']) {
            Log::info('WhatsApp notification sent successfully', [
                'user_id' => $notifiable->id,
                'phone' => $messageData['to'],
                'notification' => get_class($notification)
            ]);
            return true;
        }

        Log::error('Failed to send WhatsApp notification', [
            'user_id' => $notifiable->id,
            'phone' => $messageData['to'],
            'error' => $result['error'] ?? 'Unknown error',
            'notification' => get_class($notification)
        ]);
        return false;
    }
}
