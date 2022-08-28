<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;
use Streams\Core\Support\Facades\Streams;

class IndexSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate search indexes.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $docs = Streams::entries('docs')->where('enabled', true)->get()->values();

        $docs->each(function($item) {
        
            $item->body = strip_tags($item->body()->render([
                'entry' => $item,
            ]));

            $item->href = '/docs/' . $item->id;
        });

        $docsCore = Streams::entries('docs_core')->where('enabled', true)->get();

        $docsCore->each(function($item) {
        
            $item->body = strip_tags($item->body()->render([
                'entry' => $item,
            ]));

            $item->href = '/docs/core/' . $item->id;
        });

        $docs = $docs->merge($docsCore->values());

        file_put_contents($docsIndex = public_path('docs.json'), $docs->toJson());

        $this->info($docsIndex);

        return 0;
    }
}
