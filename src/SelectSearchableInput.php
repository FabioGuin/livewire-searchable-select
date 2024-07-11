<?php

namespace FabioGuin\LivewireSearchableSelect;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectSearchableInput extends Component
{
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

    public ?string $modelAppScope = null;

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
        ?string $modelAppScope = null
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

        if ($this->activeOptionText !== null && $this->activeOptionValue !== null) {
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
        // Check if the search query meets the minimum length requirement
        if (! $this->isSearchTermLengthValid()) {
            $this->results = collect(); // Set results to an empty collection

            return;
        }

        $this->isSelected = false; // Reset isSelected flag

        // Check if modelApp is set and the class exists
        if ($this->modelApp != null && class_exists($this->modelApp)) {
            $query = $this->modelApp::query(); // Create a query based on the modelApp

            // Check if scope is set and the method exists
            if ($this->modelAppScope) {
                if (method_exists($this->modelApp, 'scope'.ucfirst($this->modelAppScope))) {
                    $query = $query->{$this->modelAppScope}($query);
                } else {
                    $this->setMessage('Scope not found in this model!');

                    return;
                }
            }

            // Add search conditions for each search column
            $query = $query->where(function ($query) {
                foreach ($this->searchColumns as $column) {
                    $query->orWhere($column, 'like', '%'.$this->searchTherm.'%');
                }
            });

            // Add a relevance score based on the search term
            $query = $query->select('*', DB::raw("(
                CASE
                    WHEN name LIKE '{$this->searchTherm}' THEN 10 
                    WHEN name LIKE '{$this->searchTherm}%' THEN 8 
                    WHEN name LIKE '%{$this->searchTherm}%' THEN 4 
                    WHEN name LIKE '%{$this->searchTherm}' THEN 2
                    ELSE 1
                END
            ) as relevance"));

            $query = $query->orderBy('relevance', 'desc'); // Order by relevance score

            if (isset($this->searchLimitResults)) {
                $query = $query->limit($this->searchLimitResults); // Limit the number of results
            }

            $this->results = $this->buildOptions($query->get()); // Build options based on the query results

            // If there are no results, set a message
            if ($this->results->count() == 0) {
                $this->setMessage(trans('livewire-searchable-select::messages.no_results'));
            }
        } else {
            $this->setMessage('Set the correct app model!'); // Set a message to specify the correct app model
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
