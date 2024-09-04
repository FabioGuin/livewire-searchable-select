<?php

namespace FabioGuin\LivewireSearchableSelect\Traits;

use Illuminate\Support\Facades\DB;

trait Searchable
{
    private function applyScopeToQuery($query)
    {
        if ($this->modelAppScope) {
            $scopeMethod = 'scope' . ucfirst($this->modelAppScope['method']);
            $scopeParam = $this->modelAppScope['param'] ?? null;

            if (method_exists($this->modelApp, $scopeMethod)) {
                return $scopeParam ?
                    $query->{$this->modelAppScope['method']}($query, $scopeParam) :
                    $query->{$this->modelAppScope['method']}($query);
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
        foreach ($this->searchColumns as $column) {
            $cases[] = "WHEN {$column} LIKE '{$this->searchTherm}' THEN 10";
            $cases[] = "WHEN {$column} LIKE '{$this->searchTherm}%' THEN 8";
            $cases[] = "WHEN {$column} LIKE '%{$this->searchTherm}%' THEN 4";
            $cases[] = "WHEN {$column} LIKE '%{$this->searchTherm}' THEN 2";
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
