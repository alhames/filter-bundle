<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\String\Str;

abstract class AbstractFilter implements FilterInterface
{
    /** Order is important. */
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => null,
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function isFile(array $config = []): bool
    {
        return false;
    }

    public function getDefaultValue(array $config = []): mixed
    {
        return $this->normalize($config['default'] ?? $this->config['default']);
    }

    public function convert(mixed $value, array $config = []): mixed
    {
        return $this->normalize($value);
    }

    public function convertToDb(mixed $value, array $config = []): mixed
    {
        if ($this->isNull($value)) {
            return null;
        }

        $config = array_merge($this->config, $config);
        $value = $this->convert($value, $config);

        return $this->serialize($value, $config);
    }

    public function convertFromDb(mixed $value, array $config = []): mixed
    {
        if ($this->isNull($value)) {
            return null;
        }

        $config = array_merge($this->config, $config);
        $value = $this->unserialize($value, $config);
        if ($this->isNull($value)) {
            return null;
        }

        return $this->convert($value, $config);
    }

    public function convertToResponse(mixed $value, array $config = []): mixed
    {
        return $this->convert($value, $config);
    }

    public function filter(mixed $value, array $config = []): mixed
    {
        $config = array_merge($this->config, $config);
        if ($this->isNull($value)) {
            if ($config['required']) {
                throw FilterValueException::create(FilterValueException::TYPE_REQUIRED, $value, $config);
            }
            return $this->getDefaultValue($config);
        }

        foreach ($config as $type => $configValue) {
            if (null === $configValue || 'required' === $type) {
                continue;
            }

            $method = 'filter'.Str::convertCase($type, Str::CASE_CAMEL_UPPER);
            if (!method_exists($this, $method)) {
                continue;
            }
            $value = $this->$method($value, $config);
        }

        return $value;
    }

    protected function isNull(mixed $value): bool
    {
        return null === $value || '' === $value;
    }

    protected function serialize(mixed $value, array $config): mixed
    {
        return $value;
    }

    protected function unserialize(mixed $value, array $config): mixed
    {
        return $value;
    }

    protected function filterEnum(string|int|float $value, array $config): string|int|float
    {
        if (!in_array($value, $config['enum'], true)) {
            throw FilterValueException::create(FilterValueException::TYPE_ENUM, $value, $config);
        }

        return $value;
    }

    protected function filterPattern(string $value, array $config): string
    {
        $pattern = '#^'.str_replace('#', '\#', $config['pattern']).'$#iu';
        if (!preg_match($pattern, $value)) {
            throw FilterValueException::create(FilterValueException::TYPE_PATTERN, $value, $config);
        }

        return $value;
    }

    protected function filterMinLength(string $value, array $config): string
    {
        if ($config['min_length'] > mb_strlen($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_MIN_LENGTH, $value, $config);
        }

        return $value;
    }

    protected function filterMaxLength(string $value, array $config): string
    {
        if ($config['max_length'] < mb_strlen($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX_LENGTH, $value, $config);
        }

        return $value;
    }

    protected function filterLength(string $value, array $config): string
    {
        if ($config['length'] !== mb_strlen($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_LENGTH, $value, $config);
        }

        return $value;
    }

    protected function filterMaxStringSize(string $value, array $config): string
    {
        if ($config['max_string_size'] < strlen($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX_STRING_SIZE, $value, $config);
        }

        return $value;
    }

    protected function filterMin(int|float $value, array $config): int|float
    {
        if ($config['min'] > $value) {
            throw FilterValueException::create(FilterValueException::TYPE_MIN, $value, $config);
        }

        return $value;
    }

    protected function filterMax(int|float $value, array $config): int|float
    {
        if ($config['max'] < $value) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX, $value, $config);
        }

        return $value;
    }

    abstract protected function normalize(mixed $value): mixed;
    abstract protected function filterType(mixed $value, array $config): mixed;
}
