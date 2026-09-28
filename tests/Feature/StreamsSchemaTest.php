<?php

namespace Tests\Feature;

use Tests\TestCase;

class StreamsSchemaTest extends TestCase
{
    public function test_published_schema_is_valid_json_with_canonical_id()
    {
        $response = $this->get('/schema/streams.schema.json')->assertOk();

        $this->assertStringStartsWith('application/json', $response->headers->get('Content-Type'));

        $schema = json_decode($response->getContent(), true);

        $this->assertIsArray($schema);
        $this->assertSame('https://streams.dev/schema/streams.schema.json', $schema['$id'] ?? null);
    }

    public function test_published_schema_matches_the_sdk_import_and_allowed_shapes()
    {
        $schema = json_decode(file_get_contents(public_path('schema/streams.schema.json')), true);

        $this->assertSame('^@\\S+\\.json$', $schema['definitions']['import']['pattern'] ?? null);
        $this->assertSame('object', $schema['definitions']['fieldConfig']['properties']['allowed']['items']['type'] ?? null);
    }
}
