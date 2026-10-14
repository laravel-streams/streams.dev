<?php

namespace App\Livewire;

use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Edits one catalog example as a streams make node and re-renders its preview.
 * The configuration is not stored.
 */
class PlaygroundConfig extends Component
{
    #[Locked]
    public string $componentId;

    #[Locked]
    public string $label = '';

    public string $config = '';

    public ?string $preview = null;

    public ?string $gap = null;

    /** @var list<string> */
    public array $schemaErrors = [];

    public function mount(string $componentId, ?string $preview = null, ?string $gap = null): void
    {
        $component = PlaygroundCatalog::find($componentId);

        if ($component === null) {
            abort(404);
        }

        $this->componentId = $componentId;
        $this->label = $component['label'];
        $this->preview = $preview;
        $this->gap = $gap;
        $this->config = PlaygroundSchema::encodeNode($component);
    }

    public function updatedConfig(): void
    {
        $validated = PlaygroundSchema::validate($this->config);

        if (! $validated['valid']) {
            $this->schemaErrors = $validated['errors'];

            return;
        }

        $this->schemaErrors = [];

        $rendered = PlaygroundPreview::render([
            'id' => $this->componentId,
            'builder' => $validated['node']['builder'],
            'defaults' => $validated['node']['defaults'],
        ]);

        $this->preview = $rendered['html'];
        $this->gap = $rendered['gap'];
        // The preview carries package wire directives. Keep it outside this
        // component's morph, and swap the rendered stage in the browser.
        $this->dispatch('playground-rendered', id: $this->componentId, html: $this->stageHtml());
    }

    private function stageHtml(): string
    {
        return view('livewire.playground-stage', [
            'componentId' => $this->componentId,
            'label' => $this->label,
            'preview' => $this->preview,
            'gap' => $this->gap,
        ])->render();
    }

    public function render(): View
    {
        return view('livewire.playground-config');
    }
}
