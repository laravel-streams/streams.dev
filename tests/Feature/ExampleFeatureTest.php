<?php

use Tests\TestCase;

class ExampleFeatureTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
