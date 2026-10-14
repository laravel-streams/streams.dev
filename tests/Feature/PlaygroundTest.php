<?php

namespace Tests\Feature;

use App\Support\PlaygroundSeeds;
use Tests\TestCase;

class PlaygroundTest extends TestCase
{
    public function test_ui_serves_only_read_only_seeds_and_local_controls(): void
    {
        $this->get('/ui')
            ->assertOk()
            ->assertSee('data-playground-seeds', false)
            ->assertSee('data-playground-stream', false)
            ->assertSee('data-playground-actions', false)
            ->assertSee('All actions are simulated.')
            ->assertSee('Reloading, leaving, or closing this page discards all edits.')
            ->assertDontSee('wire:click', false);
    }

    public function test_each_request_receives_fresh_seeds_without_writing_fixture_files(): void
    {
        $before = $this->seedHashes();
        $first = PlaygroundSeeds::all();
        $first['people']['entries'][0]['name'] = 'Private edit';

        $second = PlaygroundSeeds::all();
        $this->assertSame(['people', 'posts', 'orders'], array_keys($second));
        $this->assertSame('Alex Morgan', $second['people']['entries'][0]['name']);
        $this->assertSame($before, $this->seedHashes());
    }

    private function seedHashes(): array
    {
        $hashes = [];

        foreach (glob(resource_path('playground/*')) as $file) {
            $hashes[basename($file)] = hash_file('sha256', $file);
        }

        return $hashes;
    }
}
