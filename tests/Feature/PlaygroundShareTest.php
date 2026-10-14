<?php

namespace Tests\Feature;

use App\Livewire\PlaygroundConfig;
use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use App\Support\PlaygroundState;
use App\Support\PlaygroundStateRejected;
use DOMDocument;
use DOMXPath;
use Livewire\Livewire;
use Tests\TestCase;

class PlaygroundShareTest extends TestCase
{
    public function test_a_link_with_a_class_name_or_code_is_rejected(): void
    {
        $component = PlaygroundCatalog::find('text-input');
        $this->assertNotNull($component);

        $className = 'Streams\\Ui\\Builders\\Inputs\\TextInput';
        $code = '<?php echo 1;';

        $this->get('/playground/text-input?state='.rawurlencode($className))->assertStatus(422);
        $this->get('/playground/text-input?state='.rawurlencode($code))->assertStatus(422);

        $classSettings = json_encode([
            'name' => 'title',
            'label' => $className,
            'placeholder' => 'Enter a title',
        ], JSON_THROW_ON_ERROR);
        $codeSettings = json_encode([
            'name' => 'title',
            'label' => 'Title',
            'placeholder' => $code,
        ], JSON_THROW_ON_ERROR);
        $builderSettings = json_encode([
            'builder' => $className,
            'name' => 'title',
            'label' => 'Title',
            'placeholder' => 'Enter a title',
        ], JSON_THROW_ON_ERROR);

        $this->get('/playground/text-input?state='.rawurlencode($classSettings))
            ->assertStatus(422)
            ->assertDontSee('data-playground-preview', false);
        $this->get('/playground/text-input?state='.rawurlencode($codeSettings))
            ->assertStatus(422)
            ->assertDontSee('data-playground-preview', false);
        $this->get('/playground/text-input?state='.rawurlencode($builderSettings))
            ->assertStatus(422)
            ->assertDontSee('data-playground-preview', false);

        try {
            PlaygroundState::link('text-input', [
                'builder' => $component['builder'],
                'defaults' => [
                    'name' => 'title',
                    'label' => $className,
                    'placeholder' => 'Enter a title',
                ],
            ]);
            $this->fail('A class name was encoded into a share link.');
        } catch (PlaygroundStateRejected) {
            $this->addToAssertionCount(1);
        }

        try {
            PlaygroundState::link('text-input', [
                'builder' => $component['builder'],
                'defaults' => [
                    'name' => 'title',
                    'label' => 'Title',
                    'placeholder' => $code,
                ],
            ]);
            $this->fail('Code was encoded into a share link.');
        } catch (PlaygroundStateRejected) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_state_round_trips_through_the_url_and_the_rendered_output_matches(): void
    {
        foreach (PlaygroundCatalog::components() as $component) {
            $node = [
                'builder' => $component['builder'],
                'defaults' => $component['defaults'],
            ];
            $url = PlaygroundState::link($component['id'], $node);
            $decodedUrl = rawurldecode($url);

            $this->assertStringNotContainsString('\\', $decodedUrl, $component['id']);
            $this->assertStringNotContainsString($component['builder'], $decodedUrl, $component['id']);
            $this->assertStringNotContainsString('<?php', $decodedUrl, $component['id']);

            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $this->assertSame($node, PlaygroundState::decode($component['id'], $query['state']), $component['id']);
        }

        $text = PlaygroundCatalog::find('text-input');
        $this->assertNotNull($text);
        $state = [
            'builder' => $text['builder'],
            'defaults' => [
                'name' => 'headline',
                'label' => 'Headline',
                'placeholder' => 'Type a headline',
            ],
        ];
        $url = PlaygroundState::link('text-input', $state);
        $rendered = PlaygroundPreview::render([
            'id' => 'text-input',
            'builder' => $state['builder'],
            'defaults' => $state['defaults'],
        ]);

        $response = $this->get($url)->assertOk();
        $this->assertSame($rendered['html'], $response->viewData('preview'));
        $this->assertStringContainsString('placeholder="Type a headline"', (string) $response->viewData('preview'));

        $href = $this->shareHref($response->getContent());
        $this->assertSame($url, $href);
        $this->assertStringNotContainsString($text['builder'], rawurldecode($href));

        $again = $this->get($href)->assertOk();
        $this->assertSame($rendered['html'], $again->viewData('preview'));
        $this->assertSame($state, $this->stateFromLink('text-input', $href));

        $editor = Livewire::test(PlaygroundConfig::class, [
            'componentId' => 'text-input',
            'preview' => PlaygroundPreview::render($text)['html'],
            'gap' => null,
        ])->set('config', PlaygroundSchema::encodeNode($state));

        $this->assertSame($url, $editor->get('shareUrl'));
        $this->assertSame($rendered['html'], $editor->instance()->preview);

        $form = PlaygroundCatalog::find('form');
        $this->assertNotNull($form);
        $formState = [
            'builder' => $form['builder'],
            'defaults' => $form['defaults'],
        ];
        $formState['defaults']['heading'] = 'Hello';
        $formState['defaults']['components'][0]['defaults']['label'] = 'Your name';
        $formUrl = PlaygroundState::link('form', $formState);
        $formRendered = PlaygroundPreview::render([
            'id' => 'form',
            'builder' => $formState['builder'],
            'defaults' => $formState['defaults'],
        ]);

        $this->assertStringNotContainsString('\\', rawurldecode($formUrl));
        $this->assertStringNotContainsString($form['builder'], rawurldecode($formUrl));
        $this->assertSame($formState, $this->stateFromLink('form', $formUrl));
        $this->get($formUrl)->assertOk()
            ->assertSee('Hello', false)
            ->assertSee('Your name', false);
        $this->assertSame(
            $formRendered['html'],
            $this->get($formUrl)->viewData('preview')
        );
    }

    /**
     * @return array{builder: string, defaults: array<string, mixed>}
     */
    private function stateFromLink(string $componentId, string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return PlaygroundState::decode($componentId, $query['state']);
    }

    private function shareHref(string $html): string
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $link = (new DOMXPath($document))->query('//*[@data-playground-share-link]')->item(0);
        $this->assertNotNull($link);

        return $link->getAttribute('href');
    }
}
