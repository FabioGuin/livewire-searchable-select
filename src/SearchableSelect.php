<?php

namespace FabioGuin\LivewireSearchableSelect;

use Livewire\Attributes\On;

trait SearchableSelect
{
    #[On('property-changed')]
    public function updatePropertyValue($property, $id): void
    {
        $this->{$property} = $id;
    }
}
