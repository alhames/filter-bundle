<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\String\Str;

class UrlFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'url',
        'enum' => null,
        'length' => null,
        'min_length' => 4,
        'max_length' => 255,
        'max_string_size' => 255,
        'pattern' => null,
        'scheme' => false,
    ];

    public static function getType(): string
    {
        return 'url';
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
        if (!Str::isUrl($value, $config['scheme'])) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $value;
    }
}
