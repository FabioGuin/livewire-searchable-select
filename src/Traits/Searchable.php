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
        return $query->select('*', DB::raw("(
            CASE
                WHEN name LIKE '{$this->searchTherm}' THEN 10 
                WHEN name LIKE '{$this->searchTherm}%' THEN 8 
                WHEN name LIKE '%{$this->searchTherm}%' THEN 4 
                WHEN name LIKE '%{$this->searchTherm}' THEN 2
                ELSE 1
                END
            ) as relevance"))->orderBy('relevance', 'desc');
    }

    private function applyLimitToQuery($query)
    {
        if (isset($this->searchLimitResults)) {
            return $query->limit($this->searchLimitResults);
        }

        return $query;
    }
}
