<?php

namespace Tests\Feature;

use App\Livewire\PlaygroundConfig;
use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\HtmlSnapshot;
use Tests\TestCase;

class PlaygroundExampleSnapshotTest extends TestCase
{
    #[DataProvider('examples')]
    public function test_example_config_renders_and_matches_snapshot(string $id): void
    {
        $component = PlaygroundCatalog::find($id);
        $this->assertNotNull($component);
        $json = PlaygroundSchema::encodeNode($component);
        $validated = PlaygroundSchema::validate($json);
        $this->assertTrue($validated['valid'], implode(' ', $validated['errors']));
        $initial = PlaygroundPreview::render($component);
        $this->assertNull($initial['gap']);

        // Exercise the same JSON update path as the example editor. Whitespace
        // makes the value change even though the example's defaults stay intact.
        $editor = Livewire::test(PlaygroundConfig::class, [
            'componentId' => $id,
            'preview' => $initial['html'],
        ])->set('config', $json."\n")
            ->assertSet('schemaErrors', [])
            ->assertSet('gap', null)
            ->assertDispatched('playground-rendered');

        $html = $editor->instance()->preview;
        $this->assertIsString($html);
        $this->assertNotSame('', trim($html));
        HtmlSnapshot::assertMatches('playground/components/'.$id, $html);
    }

    /** @return array<string, array{0: string}> */
    public static function examples(): array
    {
        return PlaygroundComponentSnapshotTest::components();
    }
}
