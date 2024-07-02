<?php

namespace FabioGuin\LivewireSelect;

use FabioGuin\LivewireSelect\View\Components\ClearButton;
use FabioGuin\LivewireSelect\View\Components\LoadingIndicator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireSearchableSelectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->registerViews();
        $this->registerViewComponents();
        $this->registerLivewireComponents();
        $this->registerConfig();
        $this->registerTranslations();
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'livewire-select');

        // Publish views for customization
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/livewire-select'),
            ], 'views');
        }
    }

    protected function registerViewComponents(): void
    {
        Blade::component('loading-indicator', LoadingIndicator::class);
        Blade::component('clear-button', ClearButton::class);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('select-input', SelectInput::class);
    }

    protected function registerConfig(): void
    {
        // Load the config file
        $this->mergeConfigFrom(
            __DIR__.'/config/livewire-select.php', 'livewire-select'
        );

        // Publish the config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/livewire-select.php' => config_path('livewire-select.php'),
            ], 'config');
        }
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'livewire-select');

        // Publish translations
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/lang' => resource_path('lang/vendor/livewire-select'),
            ], 'lang');
        }
    }
}
