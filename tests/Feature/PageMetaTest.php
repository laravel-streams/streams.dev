<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageMetaTest extends TestCase
{
    public function test_docs_pages_have_title_and_description()
    {
        $this->get('/docs/core/introduction')
            ->assertOk()
            ->assertSee('<title>Introduction · Core · Streams</title>', false)
            ->assertSee('<meta name="description" content="JSON-defined streams', false)
            ->assertSee('<link rel="icon" type="image/png" href="/favicon.png" />', false)
            ->assertSee('type="text/markdown" href="'.url('/docs/core/introduction.md').'"', false);
    }

    public function test_site_pages_have_title_and_description()
    {
        $this->get('/')->assertOk()->assertSee('<title>Streams</title>', false)->assertSee('<meta name="description"', false);
        $this->get('/docs')->assertOk()->assertSee('<title>Documentation · Streams</title>', false);
        $this->get('/addons/streams/mongodb')->assertOk()->assertSee('<title>Mongo DB · Addons · Streams</title>', false);
    }
}
