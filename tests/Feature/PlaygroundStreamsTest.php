<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Mockery;
use Mockery\MockInterface;
use Streams\Core\Criteria\Adapter\FilebaseAdapter;
use Streams\Core\Support\Facades\Streams;
use Tests\TestCase;

class PlaygroundStreamsTest extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        // Guard bootstrap as well as queries: even requesting a connection fails.
        $app->extend('db', function ($database) {
            $guard = Mockery::mock($database);
            $guard->shouldReceive('connection')->never();

            return $guard;
        });

        $app->make(Kernel::class)->bootstrap();

        $this->assertInstanceOf(MockInterface::class, $app->make('db'));

        return $app;
    }

    public function test_seeded_streams_load_without_opening_a_database_connection(): void
    {
        foreach (['people' => 6, 'posts' => 5, 'orders' => 8] as $handle => $count) {
            $stream = Streams::make($handle);

            $this->assertSame('filebase', $stream->config('source.type'));
            $this->assertInstanceOf(FilebaseAdapter::class, $stream->entries()->adapter);
            $this->assertSame($count, $stream->entries()->count());
            $this->assertCount($count, $stream->entries()->get());
        }

        $this->assertSame([], $this->app->make('db')->getConnections());
    }

    public function test_seed_records_support_filters_sorting_and_people_relationships(): void
    {
        $this->assertSame(4, Streams::entries('people')->where('active', true)->count());
        $this->assertSame(3, Streams::entries('posts')->where('status', 'published')->count());
        $this->assertSame('order-1006', Streams::entries('orders')->orderBy('total_cents', 'desc')->first()->id);

        foreach (Streams::entries('posts')->get() as $post) {
            $this->assertNotNull(Streams::entries('people')->find($post->author));
        }

        foreach (Streams::entries('orders')->get() as $order) {
            $this->assertNotNull(Streams::entries('people')->find($order->customer));
            $this->assertSame('USD', $order->currency);
            $this->assertIsInt($order->total_cents);
        }

        $this->assertNull(Streams::entries('posts')->find('post-forms')->published_at);
        $this->assertSame([], $this->app->make('db')->getConnections());
    }
}
