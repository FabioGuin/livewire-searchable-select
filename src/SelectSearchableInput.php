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

    public string $showOnOption;

    public string $valueOnOption;

    public mixed $activeValue = null;

    public int $minCharsToSearch = 0;

    public ?int $maxResultListLength = 10;

    public ?string $inputPlaceholder = null;

    public Collection $results;

    public ?string $searchTherm = null;

    public bool $isSelected = false;

    public ?string $message = null;

    public ?string $inputExtraClasses = null;

    public function mount(
        string $property,
        array $searchColumns,
        string $showOnOption,
        string $valueOnOption,
        int $minCharsToSearch,
        ?string $inputPlaceholder,
        ?string $modelApp,
        ?int $maxResultListLength = 10,
        mixed $activeValue = null,
        ?string $inputExtraClasses = null
    ): void {
        // Input-related properties
        $this->property = $property;
        $this->inputPlaceholder = $inputPlaceholder;
        $this->inputExtraClasses = $inputExtraClasses;

        // Search-related properties
        $this->minCharsToSearch = $minCharsToSearch;
        $this->maxResultListLength = $maxResultListLength;
        $this->searchColumns = $searchColumns;

        // Data properties
        $this->showOnOption = $showOnOption;
        $this->valueOnOption = $valueOnOption;
        $this->modelApp = $modelApp;

        // Active value
        $this->activeValue = $activeValue;
        $this->searchTherm = $this->activeValue;
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
        if (! $this->checkMinLength()) {
            $this->results = collect(); // Set results to an empty collection

            return;
        }

        $this->isSelected = false; // Reset isSelected flag

        // Check if modelApp is set and the class exists
        if ($this->modelApp != null && class_exists($this->modelApp)) {
            $query = $this->modelApp::query(); // Create a query based on the modelApp

            // Add search conditions for each search column
            foreach ($this->searchColumns as $column) {
                $query = $query->orWhere($column, 'like', '%'.$this->searchTherm.'%');
            }

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

            if (isset($this->maxResultListLength)) {
                $query = $query->limit($this->maxResultListLength); // Limit the number of results
            }

            $this->results = $this->buildOptions($query->get()); // Build options based on the query results

            // If there are no results, set a message
            if ($this->results->count() == 0) {
                $this->setMessage(trans('livewire-searchable-select::messages.no_results'));
            }
        } else {
            $this->setMessage(trans('Set the correct app model!')); // Set a message to specify the correct app model
        }
    }

    private function checkMinLength(): bool
    {
        if (isset($this->minCharsToSearch) and strlen($this->searchTherm) < $this->minCharsToSearch) {
            $this->setMessage(trans('livewire-searchable-select::messages.min_length', ['min' => $this->minCharsToSearch]));

            return false;
        } else {
            return true;
        }
    }

    protected function buildOptions($dataList)
    {
        return $dataList->map(function ($value) {
            return [
                'id' => $value->{$this->valueOnOption},
                'value' => preg_replace_callback('#\{(.*?)}#', fn ($matches) => $value->{$matches[1]}, $this->showOnOption),
            ];
        });
    }

    private function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    #[Locked]
    public function getValueOnOption($id, $value): void
    {
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
