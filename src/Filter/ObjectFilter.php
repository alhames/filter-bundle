<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\ConvertException;
use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Exception\InvalidConfigException;
use Alhames\FilterBundle\FilterManager;

class ObjectFilter extends AbstractCompressibleFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'format' => null,
        'type' => 'object',
        'properties' => null,
        'compress' => false,
    ];

    protected FilterManager $manager;

    public function __construct(FilterManager $manager, array $config = [])
    {
        $this->manager = $manager;
        parent::__construct($config);
    }

    public static function getType(): string
    {
        return 'object';
    }

    public function filterRequest(mixed $value, array $config = []): mixed
    {
        $config = array_merge($this->config, $config);
        if ($this->isNull($value)) {
            if ($config['required']) {
                throw FilterValueException::create(FilterValueException::TYPE_REQUIRED, $value, $config);
            }
            return $this->getDefaultValue($config);
        }

        if (null !== $config['format']) {
            $method = 'unserialize'.ucfirst($config['format']);
            if (!method_exists($this, $method)) {
                throw InvalidConfigException::create('format', $config['format'], $config);
            }

            try {
                $value = $this->$method($value);
            } catch (\Throwable $exception) {
                throw FilterValueException::create(FilterValueException::TYPE_FORMAT, $value, $config, $exception);
            }
        }

        $value = $this->runFilters($value, $config);

        return $this->handleInternal('filterRequest', $value, $config);
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

        $value = $this->runFilters($value, $config);

        return $this->handleInternal('filter', $value, $config);
    }

    public function convertToResponse(mixed $value, array $config = []): mixed
    {
        $value = $this->normalize($value);
        if (null === $value) {
            return null;
        }

        return $this->handleInternal('convertToResponse', $value, $config);
    }

    public function convert(mixed $value, array $config = []): mixed
    {
        $value = $this->normalize($value);
        if (null === $value) {
            return null;
        }

        return $this->handleInternal('convert', $value, $config);
    }

    protected function handleInternal(string $method, mixed $value, array $config): mixed
    {
        if (!isset($config['properties'])) {
            return $value;
        }

        $data = [];
        foreach ($config['properties'] as $property => $propertyConfig) {
            try {
                $data[$property] = $this->manager
                    ->getFilter($propertyConfig['type'])
                    ->$method($this->getProperty($value, $property), $propertyConfig);
            } catch (FilterValueException $exception) {
                throw $exception->prependConfigPath($property);
            }
        }

        return $data;
    }

    protected function isNull(mixed $value): bool
    {
        return null === $value || '' === $value || [] === $value;
    }

    protected function serialize(mixed $value, array $config): mixed
    {
        $method = 'serialize'.ucfirst($config['format'] ?? 'default');
        if (!method_exists($this, $method)) {
            throw InvalidConfigException::create('format', $config['format'], $config);
        }

        try {
            $data = $this->$method($value);
        } catch (\Throwable $exception) {
            throw ConvertException::create(ConvertException::TYPE_FORMAT, $value, $config, $exception)->setMethod($method);
        }

        return parent::serialize($data, $config);
    }

    protected function unserialize(mixed $value, array $config): mixed
    {
        $value = parent::unserialize($value, $config);
        $method = 'unserialize'.ucfirst($config['format'] ?? 'default');
        if (!method_exists($this, $method)) {
            throw InvalidConfigException::create('format', $config['format'], $config);
        }

        try {
            $data = $this->$method($value);
        } catch (\Throwable $exception) {
            throw ConvertException::create(ConvertException::TYPE_FORMAT, $value, $config, $exception)->setMethod($method);
        }

        return $data;
    }

    protected function normalize(mixed $value): array|object|null
    {
        if ($this->isNull($value)) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            return $value;
        }

        return null;
    }

    protected function filterType(mixed $value, array $config): array|object
    {
        if (!is_array($value) && !is_object($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $value;
    }

    protected function getProperty(array|object $value, string $property): mixed
    {
        if (is_array($value) || $value instanceof \ArrayAccess) {
            return $value[$property] ?? null;
        }

        return $value->{$property} ?? null;
    }

    protected function hasProperty(array|object $value, string $property): bool
    {
        if (is_array($value) || $value instanceof \ArrayAccess) {
            return isset($value[$property]);
        }

        return isset($value->{$property});
    }

    protected function serializeDefault(mixed $data): string
    {
        return serialize($data);
    }

    protected function unserializeDefault(string $data): mixed
    {
        return unserialize($data);
    }

    protected function serializeJson(mixed $data): string
    {
        return \json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function unserializeJson(string $data): mixed
    {
        return \json_decode($data, true);
    }
}
