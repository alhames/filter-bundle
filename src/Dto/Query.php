<?php

namespace Alhames\FilterBundle\Dto;

use Alhames\FilterBundle\Exception\FilterValueException;
use Symfony\Component\HttpFoundation\ParameterBag;

class Query extends ParameterBag
{
    /** @var FilterValueException[] */
    private array $errors;

    /**
     * @param FilterValueException[] $errors
     */
    public function __construct(array $parameters, array $errors = [])
    {
        parent::__construct($parameters);
        $this->errors = $errors;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @return FilterValueException[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
