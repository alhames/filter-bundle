<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;

class BooleanFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'boolean',
    ];

    private array $trueValues = [true, 1, '1'];
    private array $falseValues = [false, 0, '0'];

    public static function getType(): string
    {
        return 'boolean';
    }

    protected function serialize(mixed $value, array $config): int
    {
        return $value ? 1 : 0;
    }

    protected function normalize(mixed $value): ?bool
    {
        if (in_array($value, $this->trueValues, true)) {
            return true;
        }
        if (in_array($value, $this->falseValues, true)) {
            return false;
        }

        return null;
    }

    protected function filterType(mixed $value, array $config): bool
    {
        if (in_array($value, $this->trueValues, true)) {
            return true;
        }
        if (in_array($value, $this->falseValues, true)) {
            return false;
        }

        throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
    }
}
