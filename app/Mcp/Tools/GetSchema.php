<?php

namespace App\Mcp\Tools;

use App\Support\DocsQuery;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get_schema')]
#[Title('Get the stream JSON Schema')]
#[Description('Returns the JSON Schema for stream definition files (streams/*.json), the same document served at /schema/streams.schema.json. Use it to write or validate a stream: its fields, field types, routes and source config.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class GetSchema extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $schemas = DocsQuery::schemas();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'in:'.implode(',', array_keys($schemas))],
        ], [
            'name.in' => 'name must be one of: '.implode(', ', array_keys($schemas)).'.',
        ]);

        $name = $validated['name'] ?? 'streams';

        if (! isset($schemas[$name])) {
            return Response::error('The '.$name.' schema is not published on this site.');
        }

        return Response::text(file_get_contents($schemas[$name]));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->enum(array_keys(DocsQuery::schemas()) ?: ['streams'])
                ->description('Which schema to return. "streams" (the default) is the stream definition schema.')
                ->default('streams'),
        ];
    }
}
