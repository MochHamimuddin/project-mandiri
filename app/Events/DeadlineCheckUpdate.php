<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeadlineCheckUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $status;
    public $current;
    public $total;
    public $timestamp;

    public function __construct($message, $status, $current, $total, $timestamp)
    {
        $this->message = $message;
        $this->status = $status;
        $this->current = $current;
        $this->total = $total;
        $this->timestamp = $timestamp;
    }

    public function broadcastOn()
    {
        return new Channel('deadline-check-progress');
    }

    public function broadcastAs()
    {
        return 'deadline.update';
    }
}
