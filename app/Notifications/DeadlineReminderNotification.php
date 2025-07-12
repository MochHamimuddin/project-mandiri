<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\DataLaporan;
use Carbon\Carbon;
use App\Services\FonnteService;

class DeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct(DataLaporan $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['fonnte'];
    }

    public function toFonnte($notifiable)
    {
        $deadline = Carbon::parse($this->report->deadline_time)
            ->timezone('Asia/Jakarta')
            ->format('d-m-Y H:i:s');

        $message = "⚠️ *Pengingat Deadline Laporan* ⚠️\n\n"
            . "Yth. {$notifiable->nama_lengkap},\n\n"
            . "Anda memiliki laporan yang belum diupload!\n"
            . "• ID Laporan: #{$this->report->id}\n"
            . "• Deadline: *{$deadline} WIB*\n\n"
            . "Status: *BELUM UPLOAD*\n\n"
            . "Segera upload melalui sistem sebelum terlambat.\n\n"
            . "Terima kasih.";

        return [
            'to' => $notifiable->no_telp, // Changed from notelp to no_telp
            'message' => $message,
            'options' => [
                'delay' => 0,
                'priority' => 'high'
            ]
        ];
    }

    public function shouldSend($notifiable)
    {
        // Check if user has phone number, report is not uploaded, and deadline passed
        return !empty($notifiable->no_telp) &&
               $this->report->is_upload == 0 &&
               Carbon::now('Asia/Jakarta')->greaterThan($this->report->deadline_time);
    }
}
