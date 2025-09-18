<?php

namespace FabioGuin\LivewireSearchableSelect\Services;

use FabioGuin\LivewireSearchableSelect\Config\SearchableSelectConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchableSelectService
{
    public function search(SearchableSelectConfig $config, ?string $searchTerm = null): Collection
    {
        if (empty($searchTerm) || strlen($searchTerm) < $config->searchMinChars) {
            return collect();
        }

        $query = $this->buildBaseQuery($config);
        $query = $this->applySearchFilters($query, $config, $searchTerm);
        $query = $this->applyRelevanceScore($query, $config, $searchTerm);
        $query = $this->applyLimit($query, $config);

        return $query->get();
    }

    public function getSelectedOption(SearchableSelectConfig $config, mixed $value): ?object
    {
        if (empty($value)) {
            return null;
        }

        $query = $this->buildBaseQuery($config);
        $query->where($config->optionValueColumn, $value);

        return $query->first();
    }

    private function buildBaseQuery(SearchableSelectConfig $config): Builder
    {
        $model = app($config->modelApp);
        $query = $model->newQuery();

        if ($config->modelAppScope) {
            $query->{$config->modelAppScope}();
        }

        return $query;
    }

    private function applySearchFilters(Builder $query, SearchableSelectConfig $config, string $searchTerm): Builder
    {
        return $query->where(function ($query) use ($config, $searchTerm) {
            foreach ($config->searchColumns as $column) {
                $query->orWhere($column, 'like', '%' . $searchTerm . '%');
            }
        });
    }

    private function applyRelevanceScore(Builder $query, SearchableSelectConfig $config, string $searchTerm): Builder
    {
        $cases = [];
        $quotedSearchTerm = DB::getPdo()->quote($searchTerm);
        
        foreach ($config->searchColumns as $column) {
            // Sanitize column name to prevent SQL injection
            $sanitizedColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            if (empty($sanitizedColumn)) {
                continue; // Skip invalid column names
            }
            
            $cases[] = "WHEN {$sanitizedColumn} LIKE {$quotedSearchTerm} THEN 10";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT({$quotedSearchTerm}, '%') THEN 8";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT('%', {$quotedSearchTerm}, '%') THEN 4";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT('%', {$quotedSearchTerm}) THEN 2";
        }

        $caseStatement = implode(' ', $cases);

        return $query->select('*', DB::raw("(
                CASE
                    {$caseStatement}
                    ELSE 1
                END
            ) as relevance"))
            ->orderBy('relevance', 'desc');
    }

    private function applyLimit(Builder $query, SearchableSelectConfig $config): Builder
    {
        if ($config->searchLimitResults) {
            $query->limit($config->searchLimitResults);
        }

        return $query;
    }
}
