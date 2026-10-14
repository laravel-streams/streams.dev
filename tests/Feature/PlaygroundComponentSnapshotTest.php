<?php

namespace Tests\Feature;

use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\HtmlSnapshot;
use Tests\TestCase;

class PlaygroundComponentSnapshotTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function components(): array
    {
        $cases = [];

        foreach (self::catalog()['components'] as $component) {
            $cases[$component['id']] = [$component['id']];
        }

        return $cases;
    }

    #[DataProvider('components')]
    public function test_component_renders_without_error_and_matches_snapshot(string $id): void
    {
        $component = PlaygroundCatalog::find($id);
        $this->assertNotNull($component, $id);

        $first = $this->renderedPreview($id);
        $second = $this->renderedPreview($id);
        $direct = PlaygroundPreview::render($component);

        $this->assertNull($direct['gap'], $id);
        $this->assertNotSame('', trim($first), $id);
        $this->assertSame(
            HtmlSnapshot::normalize($first),
            HtmlSnapshot::normalize($second),
            $id.' render is not stable enough to snapshot.'
        );
        $this->assertSame(
            HtmlSnapshot::normalize($first),
            HtmlSnapshot::normalize((string) $direct['html']),
            $id.' page preview diverged from the renderer.'
        );

        HtmlSnapshot::assertMatches('playground/components/'.$id, $first);
    }

    private function renderedPreview(string $id): string
    {
        $response = $this->get('/playground/'.$id);
        $response->assertOk();
        $response->assertViewIs('playground.component');
        $response->assertDontSee('data-playground-gap', false);
        $this->assertStringContainsString('data-playground-preview="'.$id.'"', $response->getContent());

        $preview = $response->viewData('preview');
        $this->assertIsString($preview, $id);

        return $preview;
    }

    /**
     * @return array<string, mixed>
     */
    private static function catalog(): array
    {
        return json_decode(
            (string) file_get_contents(dirname(__DIR__, 2).'/resources/playground/catalog.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
