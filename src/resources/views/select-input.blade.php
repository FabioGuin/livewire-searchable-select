<div x-data="{ dropdownOpen: false }" class="position-relative">
    <input @click="dropdownOpen = ! dropdownOpen; $wire.set('searchTherm', null)"
           @click.away="dropdownOpen = false"
           type="text"
           wire:model="searchTherm"
           wire:input="getResults"
           wire:keydown="getResults"
           wire:keydown.delete="getResults"
           class="select-input form-control {{ $inputExtraClasses }}"
           placeholder="{{ $inputPlaceholder }}" />

    <div class="select-input-loading position-absolute top-50 end-0 p-2 translate-middle-y">
        <div wire:loading>
            <x-loading-indicator />
        </div>
    </div>

    <div wire:loading.remove class="select-input-clear-value position-absolute top-50 end-0 px-2 translate-middle-y">
        <div wire:click="clearSelectedValue">
            <x-clear-button />
        </div>
    </div>

    @if(isset($message))
        <div class="select-input-message position-absolute z-index-1000 w-100 bg-light rounded-bottom shadow-lg max-h-20 overflow-auto">
            <div class="small px-2 py-1">{{ $message }}</div>
        </div>
    @endif

    @if(isset($results) and count($results) > 0 and $isSelected == 0)
        <div x-show="dropdownOpen" class="select-input-dropdown position-absolute z-index-1000 w-100 bg-light rounded-bottom shadow-lg max-h-52 overflow-auto">

            @foreach($results as $data)
                <div wire:click="getValueOnOption('{{ addslashes(e($data['id'])) }}', '{{ addslashes(e($data['value'])) }}')"
                     wire:key="option-{{ $loop->index }}"
                     class="select-input-dropdown-option px-2 py-1 cursor-pointer">
                    {{ $data['value'] }}
                </div>
            @endforeach

        </div>
    @endif
</div>
