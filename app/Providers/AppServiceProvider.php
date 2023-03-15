<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Streams\Ui\Support\Facades\UI;

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
        UI::component('login', \App\Components\LoginForm::class);
    }
}
