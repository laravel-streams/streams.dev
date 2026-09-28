<?php

namespace App\Livewire;

use Livewire\Component;
use Streams\Core\Support\Facades\Streams;
use Streams\Ui\Builders\Forms\Form;
use Streams\Ui\Builders\Inputs\TextareaInput;
use Streams\Ui\Builders\Inputs\TextInput;
use Streams\Ui\Livewire\Forms\InteractsWithForms;

/**
 * A standalone stream-driven form for pages outside an admin panel.
 *
 * Replaces the `@livewire('form', [...])` component that streams-ui 0.x
 * shipped. streams-ui 1.0 only registers panel page components, so the
 * site registers its own `form` alias (see AppServiceProvider).
 */
class StreamForm extends Component
{
    use InteractsWithForms;

    public string $stream = '';

    public array $data = [];

    public array $buttons = [];

    public bool $submitted = false;

    public function mount(string $stream, array $buttons = []): void
    {
        $this->stream = $stream;
        $this->buttons = $buttons ?: [['type' => 'submit', 'text' => 'Submit']];
    }

    protected function getForms(): array
    {
        return ['form' => $this->form(Form::for($this, 'form'))];
    }

    public function form(Form $form): Form
    {
        $components = [];

        foreach (Streams::make($this->stream)->fields as $field) {
            if (in_array($field->handle, ['id', 'sort_order', 'order'])) {
                continue;
            }

            $input = in_array($field->input['type'] ?? null, ['textarea', 'editor'])
                ? TextareaInput::make($field->handle)
                : TextInput::make($field->handle);

            $components[] = $input->label($field->name ?? str($field->handle)->headline()->toString());
        }

        return $form->stream($this->stream)->statePath('data')->components($components);
    }

    public function onSubmit(): void
    {
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.stream-form', ['form' => $this->getForm('form')]);
    }
}
