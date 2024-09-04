<?php

namespace FabioGuin\LivewireSearchableSelect;

use FabioGuin\LivewireSearchableSelect\Traits\Searchable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectSearchableInput extends Component
{
    use Searchable;

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

    public ?string $searchTherm = null;

    public bool $isSelected = false;

    public ?string $message = null;

    public ?string $inputExtraClasses = null;

    public ?array $modelAppScope = null;

    public function mount(
        string $property,
        array $searchColumns,
        string $optionText,
        string $optionValueColumn,
        int $searchMinChars,
        ?string $inputPlaceholder,
        ?string $modelApp,
        ?int $searchLimitResults = 10,
        mixed $activeOptionText = null,
        mixed $activeOptionValue = null,
        ?string $inputExtraClasses = null,
        ?array $modelAppScope = null
    ): void {
        // Input-related properties
        $this->property = $property;
        $this->inputPlaceholder = $inputPlaceholder;
        $this->inputExtraClasses = $inputExtraClasses;

        // Search-related properties
        $this->searchMinChars = $searchMinChars;
        $this->searchLimitResults = $searchLimitResults;
        $this->searchColumns = $searchColumns;

        // Data properties
        $this->optionText = $optionText;
        $this->optionValueColumn = $optionValueColumn;
        $this->modelApp = $modelApp;
        $this->modelAppScope = $modelAppScope;

        // Active value
        $this->activeOptionText = $activeOptionText;
        $this->activeOptionValue = $activeOptionValue;

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

        if ($this->modelApp != null && class_exists($this->modelApp)) {
            $query = $this->modelApp::query();

            $query = $this->applyScopeToQuery($query);
            $query = $this->applySearchConditionsToQuery($query);
            $query = $this->applyRelevanceScoreToQuery($query);
            $query = $this->applyLimitToQuery($query);

            $this->results = $this->buildOptions($query->get());
        }
    }

    private function isSearchTermLengthValid(): bool
    {
        if (isset($this->searchMinChars) and strlen($this->searchTherm) < $this->searchMinChars) {
            $this->setMessage(trans('livewire-searchable-select::messages.min_length', ['min' => $this->searchMinChars]));

            return false;
        } else {
            return true;
        }
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
        $this->searchTherm = $value;
        $this->isSelected = true;
        $this->message = null;

        $this->dispatch('property-changed', property: $this->property, id: $id);
    }

    public function clearSelectedValue(): void
    {
        $this->searchTherm = null;
        $this->isSelected = false;
        $this->message = null;
        $this->results = collect();

        $this->dispatch('property-changed', property: $this->property, id: null);
    }
}
