<?php

namespace FabioGuin\LivewireSearchableSelect\Tests\Feature;

use FabioGuin\LivewireSearchableSelect\Tests\TestCase;

class PublishTest extends TestCase
{
    /** @test */
    public function it_publishes_the_component()
    {
        $this->artisan('vendor:publish', ['--provider' => 'FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider'])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_publishes_the_view()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider',
            '--tag' => 'views',
        ])->assertExitCode(0);
    }

    /** @test */
    public function it_publishes_the_config()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider',
            '--tag' => 'config',
        ])->assertExitCode(0);
    }

    /** @test */
    public function it_publishes_the_translations()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider',
            '--tag' => 'lang',
        ])->assertExitCode(0);
    }
}
