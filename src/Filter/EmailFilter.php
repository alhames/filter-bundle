<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\String\Str;

class EmailFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'email',
        'enum' => null,
        'length' => null,
        'min_length' => 6,
        'max_length' => 255,
        'max_string_size' => 255,
        'pattern' => null,
    ];

    public static function getType(): string
    {
        return 'email';
    }

    protected function normalize(mixed $value): ?string
    {
        if (!is_string($value) || '' === $value) {
            return null;
        }

        return $value;
    }

    protected function filterType(mixed $value, array $config): string
    {
        if (!is_string($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        $value = trim($value);
        if (!Str::isEmail($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $value;
    }
}
