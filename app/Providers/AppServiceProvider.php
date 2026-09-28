<?php

namespace App\Providers;

use App\Livewire\StreamForm;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
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
        // streams-ui 1.0 no longer registers a standalone `form` component; /ui uses this one.
        Livewire::component('form', StreamForm::class);

        UI::panel(
            Panel::make('admin')
                ->default()
                ->path('admin')
                ->brandName('Streams')
                ->middleware(['web'])
        );
    }
}
