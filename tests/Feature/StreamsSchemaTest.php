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
}
