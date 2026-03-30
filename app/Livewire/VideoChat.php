<?php

namespace App\Livewire;

use App\Events\WebRTCSignaling;
use Livewire\Component;

class VideoChat extends Component
{
    public $userId;
    public $activeInterests = [];
    public $turnUsername;
    public $turnCredential;

    public function mount()
    {
        $this->userId = 'user_' . uniqid();
        $this->activeInterests = request()->query('interests', []);
        $this->turnUsername = config('services.turn.username');
        $this->turnCredential = config('services.turn.credential');
    }

    public function sendSignal($receiverId, $data)
    {
        broadcast(new WebRTCSignaling($this->userId, $receiverId, $data))->toOthers();
    }

    public function broadcastReady()
    {
        broadcast(new WebRTCSignaling($this->userId, 'all', ['type' => 'ready']))->toOthers();
    }

    public function render()
    {
        return view('livewire.video-chat');
    }
}
