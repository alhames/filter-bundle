<?php

namespace Alhames\FilterBundle;

use Alhames\FilterBundle\Filter\FilterInterface;

class FilterManager
{
    private array $filters = [];

    public function getFilter(string $type): FilterInterface
    {
        return $this->filters[$type];
    }

    public function setFilter(string $type, FilterInterface $filter): void
    {
        $this->filters[$type] = $filter;
    }
}
