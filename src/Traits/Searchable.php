<?php

namespace FabioGuin\LivewireSearchableSelect\Traits;

use Illuminate\Support\Facades\DB;

trait Searchable
{
    private function applyScopeToQuery($query)
    {
        if ($this->modelAppScope) {
            if (method_exists($this->modelApp, 'scope'.ucfirst($this->modelAppScope))) {
                return $query->{$this->modelAppScope}();
            }
            $this->setMessage('Scope not found in this model!');

            return $query;
        }

        return $query;
    }

    private function applySearchConditionsToQuery($query)
    {
        return $query->where(function ($query) {
            foreach ($this->searchColumns as $column) {
                $query->orWhere($column, 'like', '%'.$this->searchTherm.'%');
            }
        });
    }

    private function applyRelevanceScoreToQuery($query)
    {
        $cases = [];
        $searchTerm = DB::getPdo()->quote($this->searchTherm);
        
        foreach ($this->searchColumns as $column) {
            // Sanitize column name to prevent SQL injection
            $sanitizedColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            if (empty($sanitizedColumn)) {
                continue; // Skip invalid column names
            }
            
            $cases[] = "WHEN {$sanitizedColumn} LIKE {$searchTerm} THEN 10";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT({$searchTerm}, '%') THEN 8";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT('%', {$searchTerm}, '%') THEN 4";
            $cases[] = "WHEN {$sanitizedColumn} LIKE CONCAT('%', {$searchTerm}) THEN 2";
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

    private function applyLimitToQuery($query)
    {
        if (isset($this->searchLimitResults)) {
            return $query->limit($this->searchLimitResults);
        }

        return $query;
    }
}
