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

        $docs->each(function ($item) {
            $item->href = '/docs/' . $item->id;
            $item->package = 'Streams';
        });

        $packages = ['core' => 'Core', 'api' => 'API', 'ui' => 'UI', 'sdk' => 'SDK'];

        foreach ($packages as $package => $label) {

            $packageDocs = Streams::entries('docs_' . $package)->where('enabled', true)->get();

            $packageDocs->each(function ($item) use ($package, $label) {
                $item->href = '/docs/' . $package . '/' . $item->id;
                $item->package = 'Streams ' . $label;
            });

            $docs = $docs->merge($packageDocs->values());
        }

        file_put_contents($docsIndex = public_path('docs.json'), $docs->toJson());

        $this->info($docsIndex);

        return 0;
    }
}
