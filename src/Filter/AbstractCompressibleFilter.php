<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\ConvertException;

abstract class AbstractCompressibleFilter extends AbstractFilter
{
    protected function serialize(mixed $value, array $config): mixed
    {
        if (!$config['compress']) {
            return $value;
        }

        $data = gzencode($value, 9, FORCE_GZIP);
        if (false === $data) {
            throw ConvertException::create(ConvertException::TYPE_COMPRESS, $value, $config)->setMethod('gzencode');
        }

        return $data;
    }

    protected function unserialize(mixed $value, array $config): mixed
    {
        if (!$config['compress']) {
            return $value;
        }

        $data = gzdecode($value);
        if (false === $data) {
            throw ConvertException::create(ConvertException::TYPE_COMPRESS, $value, $config)->setMethod('gzdecode');
        }

        return $data;
    }
}
