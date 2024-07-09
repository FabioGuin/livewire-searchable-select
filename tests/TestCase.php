<?php

namespace FabioGuin\LivewireSearchableSelect\Tests;

use FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider;
use Livewire\LivewireServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireSearchableSelectServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('livewire.class_namespace', 'App\\Http\\Livewire');
        $app['config']->set('livewire.view_path', resource_path('views/livewire'));
    }
}
