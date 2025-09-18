<div x-data="{ dropdownOpen: false }" class="position-relative">
    <input @click="dropdownOpen = ! dropdownOpen; $wire.set('searchTerm', null)"
           @click.away="dropdownOpen = false; $wire.set('message', null)"
           type="text"
           wire:model="searchTerm"
           wire:input="getResults; dropdownOpen = true"
           wire:keydown="getResults; dropdownOpen = true"
           wire:keydown.delete="getResults; dropdownOpen = true"
           class="select-searchable-input form-control {{ $inputExtraClasses }}"
           placeholder="{{ $inputPlaceholder }}" />

    <div class="select-searchable-input-loading position-absolute top-50 end-0 p-2 translate-middle-y">
        <div wire:loading wire:target="getResults">
            <x-loading-indicator />
        </div>
    </div>

    <div wire:loading.remove wire:target="getResults" class="select-searchable-input-clear-value position-absolute top-50 end-0 px-2 translate-middle-y">
        <div wire:click="clearSelectedValue">
            <x-clear-button />
        </div>
    </div>

    @if(isset($message))
        <div class="select-searchable-input-message position-absolute z-index-1000 w-100 bg-light rounded-bottom shadow-lg max-h-20 overflow-auto">
            <div class="small px-2 py-1">{{ $message }}</div>
        </div>
    @endif

    @if(isset($results) and count($results) > 0 and $isSelected == 0)
        <div x-show="dropdownOpen" class="select-searchable-input-dropdown position-absolute z-index-1000 w-100 bg-light rounded-bottom shadow-lg max-h-52 overflow-auto">

            @foreach($results as $data)
                <div wire:click="getValueOption('{{ addslashes(e($data['id'])) }}', '{{ addslashes(e($data['value'])) }}')"
                     wire:key="option-{{ $loop->index }}"
                     class="select-searchable-input-dropdown-option px-2 py-1 cursor-pointer">
                    {{ $data['value'] }}
                </div>
            @endforeach

        </div>
    @endif
</div>
