<?php

namespace Alhames\FilterBundle\Filter;

class DatetimeFilter extends DateFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'datetime',
        'format' => \DateTimeInterface::W3C,
        'min_date' => '1970-01-01',
        'max_date' => '2099-12-31',
    ];

    protected string $pattern = '(19[7-9][0-9]|20[0-9]{2})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?';

    public static function getType(): string
    {
        return 'datetime';
    }

    /**
     * @param \DateTimeInterface $value
     */
    protected function serialize(mixed $value, array $config): string
    {
        return $value->format('Y-m-d H:i:s');
    }
}
