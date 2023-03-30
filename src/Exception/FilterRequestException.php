<?php

namespace Alhames\FilterBundle\Exception;

use Alhames\FilterBundle\Dto\Query;

class FilterRequestException extends \RuntimeException
{
    private Query $query;

    public function __construct(Query $query)
    {
        parent::__construct('Request is not valid.');
        $this->query = $query;
    }

    /**
     * @return FilterValueException[]
     */
    public function getErrors(): array
    {
        return $this->query->getErrors();
    }
}
