<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;

class FloatFilter extends AbstractFilter
{
    public const DB_MIN_SIGNED_FLOAT = -3.402823466E+38;
    public const DB_MAX_SIGNED_FLOAT = -1.175494351E-38;
    public const DB_MIN_UNSIGNED_FLOAT = 1.175494351E-38;
    public const DB_MAX_UNSIGNED_FLOAT = 3.402823466E+38;

    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'float',
        'enum' => null,
        'min' => 0,
        'max' => self::DB_MAX_UNSIGNED_FLOAT,
        'precision' => 2,
        'format' => null,
    ];

    public static function getType(): string
    {
        return 'float';
    }

    protected function normalize(mixed $value): ?float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    protected function serialize(mixed $value, array $config): int|float
    {
        if ('integer' === $config['format']) {
            return (int) ($value * (10 ** $config['precision']));
        }

        return $value;
    }

    protected function unserialize(mixed $value, array $config): float
    {
        if ('integer' === $config['format']) {
            return $value / (10 ** $config['precision']);
        }

        return $value;
    }

    protected function filterType(mixed $value, array $config): float
    {
        if (!is_numeric($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return (float) $value;
    }
}
