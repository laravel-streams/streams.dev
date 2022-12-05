<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Streams\Core\Support\Facades\Streams;

class Form extends Component
{
    public string $stream;
    public string $entry;

    public function stream()
    {
        return Streams::make($this->stream);
    }

    public function entry()
    {
        return $this->stream()->repository()->find($this->entry);
    }

    public function submit()
    {
        $entry = $this->entry();

        $entry->fill(Request::input());

        $validator = $this->stream()->validator($entry, false);

        if ($validator->passes()) {
            return Redirect::to('ui?success='. date('U'));
        } else {
            dd($validator->messages());
        }
    }

    public function render()
    {
        return view('livewire.form', [
            'entry' => $this->entry(),
        ]);
    }
}
