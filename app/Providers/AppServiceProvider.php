<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Streams\Ui\Builders\Panels\Panel;
use Streams\Ui\Support\Facades\UI;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        UI::panel(
            Panel::make('admin')
                ->default()
                ->path('admin')
                ->brandName('Streams')
                ->middleware(['web'])
        );
    }
}
