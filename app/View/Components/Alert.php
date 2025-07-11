<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Arr;

class Alert extends Component
{
    public $config;

    public function __construct(
        string $type = null,
        string $message = null,
        string $title = null,
        int $timer = null,
        bool $showConfirmButton = null,
        string $position = null,
        string $background = null
    ) {
        $sessionAlertData = collect(session()->all())
            ->filter(fn ($value, $key) => str_starts_with($key, 'alert_'))
            ->mapWithKeys(fn ($value, $key) => [str_replace('alert_', '', $key) => $value])
            ->toArray();

        $this->config = array_merge([
            'type' => 'success',
            'message' => '',
            'title' => null,
            'timer' => 3000,
            'showConfirmButton' => false,
            'position' => 'center',
            'background' => null,
            'iconColor' => null,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33',
            'showCancelButton' => false,
            'confirmButtonText' => 'OK',
        ], $sessionAlertData, array_filter([
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'timer' => $timer,
            'showConfirmButton' => $showConfirmButton,
            'position' => $position,
            'background' => $background,
        ]));

        $this->config['title'] = $this->config['title'] ?? ucfirst($this->config['type']);
    }

    public function shouldRender()
    {
        return !empty($this->config['message']) || session()->has('alert_message');
    }

    public function render()
    {
        return view('components.alert', [
            'alertConfig' => $this->config
        ]);
    }
}
