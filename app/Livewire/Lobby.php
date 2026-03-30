<?php

namespace App\Livewire;

use Livewire\Component;

class Lobby extends Component
{
    public $interests = '';
    public $isSearching = false;

    public function startSearch()
    {
        if (empty(trim($this->interests))) {
            return redirect()->route('video-chat');
        }

        $interestArray = array_map('trim', explode(',', $this->interests));
        return redirect()->route('video-chat', ['interests' => $interestArray]);
    }

    public function render()
    {
        return view('livewire.lobby');
    }
}
