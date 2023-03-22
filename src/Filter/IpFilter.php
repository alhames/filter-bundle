<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\ConvertException;
use Alhames\FilterBundle\Exception\FilterValueException;

class IpFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'ip',
        'enum' => null,
        'pattern' => '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}',
    ];

    public static function getType(): string
    {
        return 'ip';
    }

    /**
     * @param string $value
     */
    protected function serialize(mixed $value, array $config): ?int
    {
        $ip = ip2long($value);
        if (false === $ip) {
            throw ConvertException::create(ConvertException::TYPE_IP, $value, $config)->setMethod('ip2long');
        }

        return $ip;
    }

    /**
     * @param int $value
     */
    protected function unserialize(mixed $value, array $config): ?string
    {
        $ip = long2ip($value);
        if (false === $ip) {
            throw ConvertException::create(ConvertException::TYPE_IP, $value, $config)->setMethod('long2ip');
        }

        return $ip;
    }

    protected function normalize(mixed $value): ?string
    {
        return (is_string($value) && '' !== $value) ? $value : null;
    }

    protected function filterType(mixed $value, array $config): string
    {
        if (!is_string($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $value;
    }
}
