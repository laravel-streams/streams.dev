<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Streams\Core\Http\Controller\StreamsController;
use Streams\Core\Support\Facades\Streams;

/**
 * Class AppServiceProvider
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class AppServiceProvider extends ServiceProvider
{

    /**
     * Additional service providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register any application services.
     */
    public function register()
    {

        /**
         * Register additional service
         * providers if they exist.
         */
        foreach ($this->providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        foreach (Streams::entries('pages')->all() as $page) {
            Route::streams($page->path, [
                'uses' => StreamsController::class . '@handle',
                'entry' => $page->id,
                'stream' => 'pages',
            ]);
        }
    }
}
