<?php

namespace FabioGuin\LivewireSearchableSelect\Services;

use FabioGuin\LivewireSearchableSelect\Config\SearchableSelectConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchableSelectService
{
    public function search(SearchableSelectConfig $config, ?string $searchTerm = null): Collection
    {
        if (empty($searchTerm) || strlen($searchTerm) < $config->searchMinChars) {
            return collect();
        }

        // Generate cache key for this search
        $cacheKey = $this->generateCacheKey($config, $searchTerm);

        // Try to get from cache first
        return Cache::remember($cacheKey, 300, function () use ($config, $searchTerm) {
            $query = $this->buildBaseQuery($config);
            $query = $this->applySearchFilters($query, $config, $searchTerm);

            // Optimize query by selecting only necessary columns first
            $query = $this->optimizeSelectColumns($query, $config);

            // Apply relevance scoring and ordering
            $query = $this->applyRelevanceScore($query, $config, $searchTerm);
            $query = $this->applyLimit($query, $config);

            return $query->get();
        });
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

    public function clearCache(SearchableSelectConfig $config): void
    {
        $pattern = 'searchable_select:'.md5(serialize([
            'model' => $config->modelApp,
            'scope' => $config->modelAppScope,
        ])).'*';

        // Clear cache entries matching the pattern
        $keys = Cache::getRedis()->keys($pattern);
        if (! empty($keys)) {
            Cache::getRedis()->del($keys);
        }
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
                $query->orWhere($column, 'like', '%'.$searchTerm.'%');
            }
        });
    }

    private function applyRelevanceScore(Builder $query, SearchableSelectConfig $config, string $searchTerm): Builder
    {
        if (empty($config->searchColumns)) {
            return $query;
        }

        $quotedSearchTerm = DB::getPdo()->quote($searchTerm);
        $relevanceCases = [];

        foreach ($config->searchColumns as $column) {
            // Sanitize column name to prevent SQL injection
            $sanitizedColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            if (empty($sanitizedColumn)) {
                continue; // Skip invalid column names
            }

            // Create relevance scoring based on match position and type
            $relevanceCases[] = "CASE 
                WHEN {$sanitizedColumn} = {$quotedSearchTerm} THEN 100
                WHEN {$sanitizedColumn} LIKE CONCAT({$quotedSearchTerm}, '%') THEN 80
                WHEN {$sanitizedColumn} LIKE CONCAT('%', {$quotedSearchTerm}, '%') THEN 60
                WHEN {$sanitizedColumn} LIKE CONCAT('%', {$quotedSearchTerm}) THEN 40
                ELSE 0
            END";
        }

        if (! empty($relevanceCases)) {
            // Calculate total relevance score
            $relevanceExpression = '('.implode(' + ', $relevanceCases).')';

            // Add relevance as a calculated field and order by it
            $query->addSelect(DB::raw("{$relevanceExpression} as relevance_score"))
                ->orderBy('relevance_score', 'desc');
        }

        return $query;
    }

    private function applyLimit(Builder $query, SearchableSelectConfig $config): Builder
    {
        if ($config->searchLimitResults) {
            $query->limit($config->searchLimitResults);
        }

        return $query;
    }

    private function optimizeSelectColumns(Builder $query, SearchableSelectConfig $config): Builder
    {
        // Extract columns needed for optionText from the template
        $requiredColumns = $this->extractColumnsFromTemplate($config->optionText);

        // Add the option value column
        $requiredColumns[] = $config->optionValueColumn;

        // Add search columns for relevance calculation
        $requiredColumns = array_merge($requiredColumns, $config->searchColumns);

        // Remove duplicates and select only necessary columns
        $requiredColumns = array_unique($requiredColumns);

        return $query->select($requiredColumns);
    }

    private function extractColumnsFromTemplate(string $template): array
    {
        $columns = [];
        preg_match_all('/\{([^}]+)\}/', $template, $matches);

        if (isset($matches[1])) {
            $columns = $matches[1];
        }

        return $columns;
    }

    private function generateCacheKey(SearchableSelectConfig $config, string $searchTerm): string
    {
        $keyData = [
            'model' => $config->modelApp,
            'scope' => $config->modelAppScope,
            'search_columns' => $config->searchColumns,
            'option_text' => $config->optionText,
            'option_value' => $config->optionValueColumn,
            'limit' => $config->searchLimitResults,
            'search_term' => $searchTerm,
        ];

        return 'searchable_select:'.md5(serialize($keyData));
    }
}
