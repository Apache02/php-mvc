<?php

namespace fw\data;


use fw\db\ActiveQuery;

class DataProvider
{
    /** @var \fw\db\ActiveQuery */
    public $query = null;

    public $pageSize = 100;
    public $page = 1;

    private $needUpdateCounters = true;
    private $totalCount = 0;
    private $_models = null;

    private $sortedBy = null;
    private $sortedAscending = null;


    public function __construct($config = null)
    {
        if ($config) {
            foreach ($config as $attributeName => $value) {
                $this->$attributeName = $value;
            }
        }
    }

    public function updateCounters()
    {
        $query = clone $this->query;
        $this->totalCount = $query->count();
        $this->needUpdateCounters = false;
        $this->_models = null;
    }

    public function getModels()
    {
        if ($this->needUpdateCounters) {
            $this->updateCounters();
        }
        if ($this->_models === null) {
            /** @var \fw\db\ActiveQuery $query */
            $query = clone $this->query;
            $page = max(0, $this->page - 1);
            if ($this->sortedBy) {
                $query->orderBy([
                    $this->sortedBy => $this->sortedAscending ? SORT_ASC : SORT_DESC,
                ]);
            }
            $query->limit($page * $this->pageSize, $this->pageSize);
            $this->_models = $query->all();
        }
        return $this->_models;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function getTotalPages()
    {
        return max(1, ceil($this->totalCount / $this->pageSize));
    }

    public function sort($attributeName, $sortedAscending = true)
    {
        $this->sortedBy = $attributeName;
        $this->sortedAscending = $sortedAscending;
    }

    public function getSortedBy()
    {
        return $this->sortedBy;
    }

    public function getSortAscending()
    {
        return $this->sortedAscending;
    }

    public function getSortCssClass($attributeName)
    {
        if ($attributeName == $this->sortedBy) {
            return $this->sortedAscending
                ? 'sorted ascending'
                : 'sorted descending';
        }
        return '';
    }

    public function getSortUrl($uri, $attributeName)
    {
        $sortDirection = $this->sortedBy == $attributeName && $this->sortedAscending ? 'desc' : 'asc';
        return "$uri?sort={$attributeName}&sort-dir=$sortDirection";
    }

}
