<?php

namespace Alhames\FilterBundle\Exception;

class FilterRequestException extends \RuntimeException implements FilterExceptionInterface
{
    /** @var FilterValueException[] */
    private array $errors;

    /**
     * @param FilterValueException[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Request is not valid.');
        $this->errors = $errors;
    }

    /**
     * @return FilterValueException[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
