<?php

namespace FabioGuin\LivewireSearchableSelect\Tests\Unit;

use FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider;
use FabioGuin\LivewireSearchableSelect\SelectSearchableInput;
use FabioGuin\LivewireSearchableSelect\Tests\TestCase;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireSearchableSelectServiceProvider::class,
        ];
    }

    /** @test */
    public function it_registers_the_service_provider()
    {
        $this->assertTrue($this->app->providerIsLoaded(LivewireSearchableSelectServiceProvider::class));
    }

    /** @test */
    public function it_registers_the_livewire_component()
    {
        $component = Livewire::new('select-searchable-input');
        $this->assertInstanceOf(SelectSearchableInput::class, $component);
    }

    /** @test */
    public function it_registers_the_blade_components()
    {
        $this->assertStringContainsString('loading-indicator', Blade::compileString('<x-loading-indicator />'));
        $this->assertStringContainsString('clear-button', Blade::compileString('<x-clear-button />'));
    }
}
