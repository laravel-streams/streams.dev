<?php

namespace App\Support;

use Livewire\Component;

/**
 * In-memory Livewire host for playground builders that read component state.
 * It is not a routed page.
 */
class PlaygroundHost extends Component
{
    public array $data = [];

    public function render()
    {
        return view('playground.host');
    }
}
