<?php

namespace FabioGuin\LivewireSearchableSelect;

use FabioGuin\LivewireSearchableSelect\Config\SearchableSelectConfig;
use FabioGuin\LivewireSearchableSelect\Services\SearchableSelectService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectSearchableInput extends Component
{
    private ?SearchableSelectConfig $config = null;

    private ?SearchableSelectService $searchService = null;

    public string $property;

    public string $modelApp;

    public array $searchColumns;

    public string $optionText;

    public string $optionValueColumn;

    public mixed $activeOptionText = null;

    public mixed $activeOptionValue = null;

    public int $searchMinChars = 0;

    public ?int $searchLimitResults = 10;

    public ?string $inputPlaceholder = null;

    public Collection $results;

    public ?string $searchTerm = null;

    public bool $isSelected = false;

    public ?string $message = null;

    public ?string $inputExtraClasses = null;

    public ?string $modelAppScope = null;

    public function mount(
        string $property,
        array $searchColumns,
        string $optionText,
        string $optionValueColumn,
        int $searchMinChars = 0,
        ?string $inputPlaceholder = null,
        ?string $modelApp = null,
        ?int $searchLimitResults = 10,
        mixed $activeOptionText = null,
        mixed $activeOptionValue = null,
        ?string $inputExtraClasses = null,
        ?string $modelAppScope = null
    ): void {
        // Initialize service and config
        $this->initializeService();
        $this->initializeConfig(
            $property,
            $searchColumns,
            $optionText,
            $optionValueColumn,
            $searchMinChars,
            $inputPlaceholder,
            $modelApp,
            $searchLimitResults,
            $activeOptionText,
            $activeOptionValue,
            $inputExtraClasses,
            $modelAppScope
        );

        // Set legacy properties for backward compatibility
        $this->property = $property;
        $this->inputPlaceholder = $inputPlaceholder;
        $this->inputExtraClasses = $inputExtraClasses;
        $this->searchMinChars = $searchMinChars;
        $this->searchLimitResults = $searchLimitResults;
        $this->searchColumns = $searchColumns;
        $this->optionText = $optionText;
        $this->optionValueColumn = $optionValueColumn;
        $this->modelApp = $modelApp;
        $this->modelAppScope = $modelAppScope;
        $this->activeOptionText = $activeOptionText;
        $this->activeOptionValue = $activeOptionValue;

        // Initialize results collection
        $this->results = collect();

        // Set initial state
        $this->isSelected = ! is_null($this->activeOptionValue);

        if (
            $this->activeOptionText !== null
            && $this->activeOptionText !== ''
            && $this->activeOptionValue !== null
            && $this->activeOptionValue !== ''
        ) {
            $this->getValueOption($this->activeOptionValue, $this->activeOptionText);
        }
    }

    public function render()
    {
        return view('livewire-searchable-select::select-searchable-input');
    }

    private function initializeService(): void
    {
        if ($this->searchService === null) {
            $this->searchService = app(SearchableSelectService::class);
        }
    }

    private function initializeConfig(
        string $property,
        array $searchColumns,
        string $optionText,
        string $optionValueColumn,
        int $searchMinChars,
        ?string $inputPlaceholder,
        ?string $modelApp,
        ?int $searchLimitResults,
        mixed $activeOptionText,
        mixed $activeOptionValue,
        ?string $inputExtraClasses,
        ?string $modelAppScope
    ): void {
        if ($this->config === null) {
            $this->config = new SearchableSelectConfig(
                property: $property,
                searchColumns: $searchColumns,
                optionText: $optionText,
                optionValueColumn: $optionValueColumn,
                searchMinChars: $searchMinChars,
                inputPlaceholder: $inputPlaceholder,
                modelApp: $modelApp,
                searchLimitResults: $searchLimitResults,
                activeOptionText: $activeOptionText,
                activeOptionValue: $activeOptionValue,
                inputExtraClasses: $inputExtraClasses,
                modelAppScope: $modelAppScope
            );
        }
    }

    /**
     * Get the search results based on the search query and modelApp.
     * If the search query does not meet the minimum length requirement, an empty collection is returned.
     * If the modelApp is not valid, a message is set notifying to set the correct app model.
     */
    public function getResults(): void
    {
        if (! $this->isSearchTermLengthValid()) {
            $this->results = collect();

            return;
        }

        $this->isSelected = false;

        // Initialize service if not already done
        $this->initializeService();

        if ($this->modelApp != null && class_exists($this->modelApp)) {
            $this->results = $this->searchService->search($this->config, $this->searchTerm);
            $this->results = $this->buildOptions($this->results);
        }
    }

    private function isSearchTermLengthValid(): bool
    {
        if (strlen($this->searchTerm) < $this->searchMinChars) {
            $this->setMessage(trans('livewire-searchable-select::messages.min_length', ['min' => $this->searchMinChars]));

            return false;
        }

        return true;
    }

    protected function buildOptions($dataList): Collection
    {
        return $dataList->map(function ($value) {
            return [
                'id' => $value->{$this->optionValueColumn},
                'value' => preg_replace_callback('#\{(.*?)}#', fn ($matches) => $value->{$matches[1]}, $this->optionText),
            ];
        });
    }

    private function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    #[Locked]
    public function getValueOption($id, $value): void
    {
        $value = htmlspecialchars_decode($value, ENT_QUOTES);
        $this->searchTerm = $value;
        $this->isSelected = true;
        $this->message = null;

        $this->dispatch('property-changed', property: $this->property, id: $id);
    }

    public function clearSelectedValue(): void
    {
        $this->searchTerm = null;
        $this->isSelected = false;
        $this->message = null;
        $this->results = collect();

        $this->dispatch('property-changed', property: $this->property, id: null);
    }
}
