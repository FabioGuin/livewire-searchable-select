<?php

namespace FabioGuin\LivewireSearchableSelect\Config;

class SearchableSelectConfig
{
    public function __construct(
        public string $property,
        public array $searchColumns,
        public string $optionText,
        public string $optionValueColumn,
        public int $searchMinChars = 0,
        public ?string $inputPlaceholder = null,
        public ?string $modelApp = null,
        public ?int $searchLimitResults = 10,
        public mixed $activeOptionText = null,
        public mixed $activeOptionValue = null,
        public ?string $inputExtraClasses = null,
        public ?string $modelAppScope = null
    ) {}

    public static function fromArray(array $config): self
    {
        return new self(
            property: $config['property'],
            searchColumns: $config['searchColumns'],
            optionText: $config['optionText'],
            optionValueColumn: $config['optionValueColumn'],
            searchMinChars: $config['searchMinChars'] ?? 0,
            inputPlaceholder: $config['inputPlaceholder'] ?? null,
            modelApp: $config['modelApp'] ?? null,
            searchLimitResults: $config['searchLimitResults'] ?? 10,
            activeOptionText: $config['activeOptionText'] ?? null,
            activeOptionValue: $config['activeOptionValue'] ?? null,
            inputExtraClasses: $config['inputExtraClasses'] ?? null,
            modelAppScope: $config['modelAppScope'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'property' => $this->property,
            'searchColumns' => $this->searchColumns,
            'optionText' => $this->optionText,
            'optionValueColumn' => $this->optionValueColumn,
            'searchMinChars' => $this->searchMinChars,
            'inputPlaceholder' => $this->inputPlaceholder,
            'modelApp' => $this->modelApp,
            'searchLimitResults' => $this->searchLimitResults,
            'activeOptionText' => $this->activeOptionText,
            'activeOptionValue' => $this->activeOptionValue,
            'inputExtraClasses' => $this->inputExtraClasses,
            'modelAppScope' => $this->modelAppScope,
        ];
    }
}
