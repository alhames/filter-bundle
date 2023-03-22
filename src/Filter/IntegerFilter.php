<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;

class IntegerFilter extends AbstractFilter
{
    public const DB_MIN_SIGNED_INT = -2_147_483_648;
    public const DB_MAX_SIGNED_INT = 2_147_483_647;
    public const DB_MAX_UNSIGNED_INT = 4_294_967_295;

    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'integer',
        'enum' => null,
        'min' => 0,
        'max' => self::DB_MAX_UNSIGNED_INT,
    ];

    public static function getType(): string
    {
        return 'integer';
    }

    protected function normalize(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    protected function filterType(mixed $value, array $config): int
    {
        if (!is_numeric($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return (int) $value;
    }
}
