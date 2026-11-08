<?php

namespace Tests\Feature;

use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPhpExample;
use Tests\TestCase;

class PlaygroundPhpExampleTest extends TestCase
{
    public function test_every_component_page_shows_a_php_snippet_with_resolvable_imports(): void
    {
        foreach (PlaygroundCatalog::components() as $component) {
            $code = PlaygroundPhpExample::render($component);

            $this->get(route('playground.component', $component['id']))
                ->assertOk()
                ->assertSee('data-playground-php="'.$component['id'].'"', false)
                ->assertSee(PlaygroundPhpExample::filename($component))
                ->assertSee('data-playground-copy', false)
                ->assertSee(e($code), false);

            preg_match_all('/^use (Streams\\\\Ui\\\\[A-Za-z\\\\]+);$/m', $code, $matches);

            $this->assertContains($component['builder'], $matches[1], $component['id']);

            foreach ($matches[1] as $class) {
                $this->assertTrue(class_exists($class), $class.' must exist in streams/ui.');
            }
        }
    }

    public function test_snippet_is_built_from_catalog_defaults(): void
    {
        $code = PlaygroundPhpExample::render(PlaygroundCatalog::find('text-input'));

        $this->assertStringContainsString("TextInput::make('title')", $code);
        $this->assertStringContainsString("->label('Title')", $code);
    }
}
