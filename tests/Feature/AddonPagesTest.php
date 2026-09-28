<?php

namespace Tests\Feature;

use Tests\TestCase;

class AddonPagesTest extends TestCase
{
    public function test_catalog_packages_render()
    {
        $this->get('/addons/streams/core')->assertOk();
        $this->get('/addons/streams/mongodb')->assertOk()->assertSee('streams/mongodb');
        $this->get('/addons/@laravel-streams/api-client')->assertOk();
    }

    public function test_unknown_package_returns_not_found()
    {
        $this->get('/addons/ryanthompson/mongodb')->assertNotFound();
        $this->get('/addons/nope/nope')->assertNotFound();
    }
}
