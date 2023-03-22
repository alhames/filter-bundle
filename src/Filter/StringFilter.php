<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;

class StringFilter extends AbstractCompressibleFilter
{
    public const DB_MAX_TEXT = 65_535;

    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'string',
        'compress' => false,
        'trim' => true,
        'enum' => null,
        'length' => null,
        'min_length' => null,
        'max_length' => null,
        'max_string_size' => self::DB_MAX_TEXT,
        'pattern' => null,
    ];

    public static function getType(): string
    {
        return 'string';
    }

    protected function normalize(mixed $value): ?string
    {
        if ('' === $value) {
            return null;
        }
        if (false === $value) {
            return '0';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return null;
    }

    protected function filterType(mixed $value, array $config): string
    {
        if (!is_scalar($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $this->normalize($value);
    }

    protected function filterTrim(string $value, array $config): string
    {
        return $config['trim'] ? trim($value) : $value;
    }
}
