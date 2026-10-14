<?php

namespace App\Support;

use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Streams\Ui\Builders\Inputs\EditorInput;

/** Supplies the Livewire context expected by the package's legacy editor view. */
class PlaygroundEditor extends Component
{
    #[Locked]
    public string $name = 'content';

    #[Locked]
    public string $id = 'playground-content';

    public string $value = '';

    #[Locked]
    public string $language = 'html';

    public function mount(string $name, string $id, string $value = ''): void
    {
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
    }

    public function htmlAttributes(): ComponentAttributeBag
    {
        return $this->editor()->getHtmlAttributeBag();
    }

    public function render(): View
    {
        return $this->editor()->render();
    }

    private function editor(): EditorInput
    {
        return EditorInput::for($this, $this->name)->id($this->id);
    }
}
