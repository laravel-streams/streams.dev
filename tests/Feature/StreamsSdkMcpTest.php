<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Streams\Core\Support\Facades\Streams;
use Streams\Sdk\Mcp\Prompts\DesignStream;
use Streams\Sdk\Mcp\Resources\StreamDefinition;
use Streams\Sdk\Mcp\Resources\StreamDefinitionSchema;
use Streams\Sdk\Mcp\StreamsServer;
use Streams\Sdk\Mcp\Tools\CreateEntry;
use Streams\Sdk\Mcp\Tools\DefinitionSchema;
use Streams\Sdk\Mcp\Tools\DeleteEntry;
use Streams\Sdk\Mcp\Tools\DescribeStream;
use Streams\Sdk\Mcp\Tools\EntrySchema;
use Streams\Sdk\Mcp\Tools\HowTo;
use Streams\Sdk\Mcp\Tools\ListEntries;
use Streams\Sdk\Mcp\Tools\ListStreams;
use Streams\Sdk\Mcp\Tools\MakeAddon;
use Streams\Sdk\Mcp\Tools\MakeStream;
use Streams\Sdk\Mcp\Tools\ReadDoc;
use Streams\Sdk\Mcp\Tools\ReadEntry;
use Streams\Sdk\Mcp\Tools\SearchDocs;
use Streams\Sdk\Mcp\Tools\StreamsStatus;
use Streams\Sdk\Mcp\Tools\UpdateEntry;
use Streams\Sdk\Mcp\Tools\ValidateStreamDefinition;
use Tests\TestCase;

/**
 * The SDK's MCP server, exercised through streams.dev because that is a booted
 * SALLT app with real streams installed — the same thing an agent gets pointed
 * at. Everything this writes lands under storage/, never in streams/.
 */
class StreamsSdkMcpTest extends TestCase
{
    protected string $scratch;

    protected string $definitions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scratch = storage_path('app/mcp-sdk-test');
        $this->definitions = $this->scratch.'/streams';

        File::ensureDirectoryExists($this->definitions);

        Config::set('streams.sdk.definitions.path', $this->definitions);
        Config::set('streams.sdk.mcp.read_only', false);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->scratch);

        parent::tearDown();
    }

    public function test_the_server_registers_its_whole_surface(): void
    {
        StreamsServer::tools()->assertRegistered([
            StreamsStatus::class,
            HowTo::class,
            ListStreams::class,
            DescribeStream::class,
            DefinitionSchema::class,
            EntrySchema::class,
            ValidateStreamDefinition::class,
            ListEntries::class,
            ReadEntry::class,
            SearchDocs::class,
            ReadDoc::class,
            CreateEntry::class,
            UpdateEntry::class,
            DeleteEntry::class,
            MakeStream::class,
            MakeAddon::class,
        ]);

        StreamsServer::prompts()->assertRegistered([DesignStream::class]);
    }

    public function test_status_reports_packages_streams_and_next_steps(): void
    {
        StreamsServer::tool(StreamsStatus::class)
            ->assertOk()
            ->assertName('streams-status')
            ->assertStructuredContent(function ($content) {
                $content->where('packages.streams/core.installed', true)
                    ->where('streams.registered', fn ($count) => $count > 0)
                    ->etc();
            })
            ->assertSee('next_steps');
    }

    public function test_describe_stream_returns_fields_and_rules_not_guesses(): void
    {
        StreamsServer::tool(DescribeStream::class, ['stream' => 'docs'])
            ->assertOk()
            ->assertStructuredContent(function ($content) {
                $content->where('id', 'docs')
                    ->where('key', 'id')
                    ->where('source.type', 'filebase')
                    ->where('fields.title.type', 'string')
                    ->where('fields.title.required', true)
                    ->etc();
            });

        StreamsServer::tool(DescribeStream::class, ['stream' => 'nope_not_here'])
            ->assertHasErrors(['is not registered'])
            ->assertSee('Registered:');
    }

    public function test_list_entries_refuses_an_unknown_field_instead_of_returning_nothing(): void
    {
        StreamsServer::tool(ListEntries::class, [
            'stream' => 'docs',
            'where' => [['field' => 'definitely_not_a_field', 'value' => 'x']],
        ])->assertHasErrors(['not a field'])->assertSee('Known fields');

        StreamsServer::tool(ListEntries::class, ['stream' => 'docs', 'limit' => 2])
            ->assertOk()
            ->assertStructuredContent(fn ($json) => $json->where('returned', 2)->where('total', fn ($total) => $total > 0)->etc());

        // A list is for recognising rows, not for spending an agent's context on
        // bodies: ask for the columns you want and the key always comes back.
        StreamsServer::tool(ListEntries::class, [
            'stream' => 'docs',
            'limit' => 3,
            'fields' => ['title'],
        ])
            ->assertOk()
            ->assertStructuredContent(fn ($json) => $json->where('fields', ['id', 'title'])->etc())
            ->assertDontSee('shortened_fields');

        // Docs bodies are long, so the full read is opt-in.
        StreamsServer::tool(ListEntries::class, ['stream' => 'docs', 'limit' => 2])
            ->assertOk()
            ->assertSee('shortened_fields');

        StreamsServer::tool(ListEntries::class, [
            'stream' => 'docs',
            'fields' => ['nope'],
        ])->assertHasErrors(['not a field']);
    }

    public function test_validation_catches_what_core_ignores_and_what_core_needs(): void
    {
        StreamsServer::tool(ValidateStreamDefinition::class, [
            'definition' => [
                'name' => 'Widgets',
                'config' => ['source' => ['type' => 'postgres']],
                'fields' => [
                    ['handle' => 'BadHandle', 'type' => 'string'],
                    ['field' => 'label_ish', 'type' => 'string'],
                ],
            ],
        ])
            ->assertOk()
            ->assertStructuredContent(function ($content) {
                $content->where('valid', false)
                    ->where('errors', fn ($errors) => count($errors) === 3)
                    ->etc();
            })
            ->assertSee([
                'is not a Repository adapter',
                'must be snake_case',
                'is not a field property',
            ]);

        StreamsServer::tool(ValidateStreamDefinition::class, [
            'definition' => [
                'name' => 'Widgets',
                'fields' => [['handle' => 'name', 'type' => 'string']],
            ],
        ])->assertOk()->assertStructuredContent(fn ($json) => $json->where('valid', true)->etc());

        // An absolute source path silently mirrors a directory tree into the project.
        StreamsServer::tool(ValidateStreamDefinition::class, [
            'definition' => [
                'name' => 'Widgets',
                'fields' => [['handle' => 'name', 'type' => 'string']],
                'config' => ['source' => ['type' => 'filebase', 'format' => 'json', 'path' => '/Users/me/app/data/widgets']],
            ],
        ])
            ->assertOk()
            ->assertStructuredContent(fn ($json) => $json->where('valid', true)->where('warnings', fn ($w) => count($w) > 0)->etc())
            ->assertSee('relative to the project root');
    }

    public function test_make_stream_writes_validates_and_registers(): void
    {
        $this->makeProbeStream();

        // Registered for this process, so the next tools can see it.
        $this->assertTrue(Streams::exists('mcp_probe'));

        StreamsServer::tool(DescribeStream::class, ['stream' => 'mcp_probe'])
            ->assertOk()
            ->assertStructuredContent(function ($content) {
                $content->where('id', 'mcp_probe')
                    ->where('fields.label.required', true)
                    ->etc();
            });

        // A second write without force is refused rather than clobbering.
        StreamsServer::tool(MakeStream::class, [
            'id' => 'mcp_probe',
            'definition' => ['name' => 'Overwritten', 'fields' => [['handle' => 'x', 'type' => 'string']]],
        ])->assertHasErrors(['already exists']);

        // An invalid definition is refused and nothing lands on disk.
        StreamsServer::tool(MakeStream::class, [
            'id' => 'mcp_broken',
            'definition' => [
                'name' => 'Broken',
                'config' => ['source' => ['type' => 'nonsense']],
                'fields' => [['handle' => 'a', 'type' => 'string']],
            ],
        ])->assertHasErrors(['is not valid and was not written', 'Repository adapter']);

        $this->assertFileDoesNotExist($this->definitions.'/mcp_broken.json');
    }

    public function test_entries_are_validated_and_protected_fields_are_never_shown(): void
    {
        $this->makeProbeStream();

        // No id passed in: the uuid field fills itself in, as it does in core.
        StreamsServer::tool(CreateEntry::class, [
            'stream' => 'mcp_probe',
            'attributes' => ['label' => 'First'],
        ])
            ->assertOk()
            ->assertStructuredContent(fn ($json) => $json
                ->where('created', true)
                ->where('key', fn ($key) => is_string($key) && $key !== '')
                ->etc());

        // Required rule from the definition, not a guess.
        StreamsServer::tool(CreateEntry::class, [
            'stream' => 'mcp_probe',
            'attributes' => ['notes' => 'no label'],
        ])->assertHasErrors(['failed validation']);

        // Unknown handle.
        StreamsServer::tool(CreateEntry::class, [
            'stream' => 'mcp_probe',
            'attributes' => ['label' => 'Ok', 'nope' => 'x'],
        ])->assertHasErrors(['not a field']);

        // Core hides protected values but never refuses them: this tool does.
        StreamsServer::tool(CreateEntry::class, [
            'stream' => 'mcp_probe',
            'attributes' => ['label' => 'Secretive', 'secret' => 'hunter2'],
        ])->assertHasErrors(['protected']);

        StreamsServer::tool(ListEntries::class, ['stream' => 'mcp_probe'])
            ->assertOk()
            ->assertSee('First')
            ->assertDontSee('hunter2');
    }

    public function test_update_read_and_delete_touch_only_what_exists(): void
    {
        $this->makeProbeStream();

        StreamsServer::tool(CreateEntry::class, [
            'stream' => 'mcp_probe',
            'attributes' => ['id' => 'probe-1', 'label' => 'Before'],
        ])->assertOk();

        StreamsServer::tool(UpdateEntry::class, [
            'stream' => 'mcp_probe',
            'key' => 'probe-1',
            'attributes' => ['label' => 'After'],
        ])->assertOk()->assertStructuredContent(fn ($json) => $json->where('updated', true)->etc())->assertSee('After');

        StreamsServer::tool(ReadEntry::class, ['stream' => 'mcp_probe', 'key' => 'probe-1'])
            ->assertOk()
            ->assertSee('After');

        StreamsServer::tool(DeleteEntry::class, ['stream' => 'mcp_probe', 'key' => 'probe-1'])
            ->assertOk()
            ->assertStructuredContent(fn ($json) => $json->where('deleted', true)->etc());

        StreamsServer::tool(DeleteEntry::class, ['stream' => 'mcp_probe', 'key' => 'probe-1'])
            ->assertHasErrors(['No [mcp_probe] entry']);
    }

    public function test_read_only_mode_hides_every_write_tool(): void
    {
        Config::set('streams.sdk.mcp.read_only', true);

        StreamsServer::tools()->assertNotRegistered([
            CreateEntry::class,
            UpdateEntry::class,
            DeleteEntry::class,
            MakeStream::class,
            MakeAddon::class,
        ])->assertRegistered([
            StreamsStatus::class,
            DescribeStream::class,
            ValidateStreamDefinition::class,
        ]);
    }

    public function test_docs_tools_read_the_installed_pages(): void
    {
        StreamsServer::tool(SearchDocs::class, ['query' => 'criteria', 'limit' => 5])
            ->assertOk()
            ->assertSee('criteria');

        StreamsServer::tool(ReadDoc::class, ['id' => 'no/such/page.md'])
            ->assertHasErrors(['No installed page']);
    }

    public function test_how_to_and_definition_schema_answer_without_arguments(): void
    {
        StreamsServer::tool(HowTo::class)
            ->assertOk()
            ->assertSee(['start-here', 'add-a-stream', 'typed-generation', 'agent-setup']);

        StreamsServer::tool(DefinitionSchema::class)
            ->assertOk()
            ->assertSee(['field_types', 'streams/', 'snake_case']);
    }

    public function test_resources_and_prompt_return_the_model(): void
    {
        StreamsServer::resource(StreamDefinitionSchema::class)
            ->assertOk()
            ->assertSee('fields');

        StreamsServer::resource(StreamDefinition::class, ['id' => 'docs'])
            ->assertOk()
            ->assertSee('fields');

        StreamsServer::prompt(DesignStream::class, ['domain' => 'support tickets'])
            ->assertOk()
            ->assertSee(['support tickets', 'validate-stream-definition', 'streams:schema']);
    }

    /**
     * Write the probe stream once through the real tool, then hand it to core.
     */
    protected function makeProbeStream(): void
    {
        if (Streams::exists('mcp_probe')) {
            return;
        }

        StreamsServer::tool(MakeStream::class, [
            'id' => 'mcp_probe',
            'definition' => [
                'name' => 'MCP Probes',
                'config' => ['source' => ['type' => 'filebase', 'format' => 'json', 'path' => 'storage/app/mcp-sdk-test/data']],
                'fields' => [
                    ['handle' => 'id', 'type' => 'uuid', 'required' => true, 'unique' => true, 'config' => ['default' => true]],
                    ['handle' => 'label', 'type' => 'string', 'required' => true],
                    ['handle' => 'notes', 'type' => 'string'],
                    ['handle' => 'secret', 'type' => 'string', 'protected' => true],
                ],
            ],
        ])->assertOk()->assertStructuredContent(fn ($json) => $json->where('written', true)->etc());

        $this->assertTrue(Streams::exists('mcp_probe'));
    }
}