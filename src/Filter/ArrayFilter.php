<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;

class ArrayFilter extends ObjectFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'array',
        'format' => null,
        'min_items' => null,
        'max_items' => null,
        'keys' => null,
        'items' => null,
        'compress' => false,
    ];

    public static function getType(): string
    {
        return 'array';
    }

    public function isFile(array $config = []): bool
    {
        if (!isset($config['items']['type'])) {
            return false;
        }

        return $this->manager->getFilter($config['items']['type'])->isFile($config['items']);
    }

    protected function normalize(mixed $value): ?array
    {
        $value = parent::normalize($value);
        if (!is_object($value)) {
            return $value;
        }
        if ($value instanceof \Traversable) {
            return iterator_to_array($value);
        }

        return null;
    }

    protected function filterType(mixed $value, array $config): array
    {
        if ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        } elseif (!is_array($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        return $value;
    }

    protected function filterMinItems(array $value, array $config): array
    {
        if ($config['min_items'] > count($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_MIN_ITEMS, $value, $config);
        }

        return $value;
    }

    protected function filterMaxItems(array $value, array $config): array
    {
        if ($config['max_items'] < count($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX_ITEMS, $value, $config);
        }

        return $value;
    }

    protected function handleInternal(string $method, mixed $value, array $config): mixed
    {
        if (!empty($config['keys'])) {
            $keyFilter = $this->manager->getFilter($config['keys']['type']);
            $data = [];
            foreach ($value as $k => $v) {
                $key = $keyFilter->$method($k, $config['keys']);
                $data[$key] = $v;
            }
        } else {
            $data = array_values($value);
        }

        if (empty($config['items'])) {
            return $data;
        }

        $filter = $this->manager->getFilter($config['items']['type']);
        foreach ($data as &$v) {
            $v = $filter->$method($v, $config['items']);
        }
        unset($v);

        return $data;
    }

    protected function serializeCsv(array $data): string
    {
        return array_reduce($data, function (?string $carry, string $item) {
            $carry .= null !== $carry ? ',' : '';
            if (str_contains($item, '"')) {
                $carry .= '"'.str_replace('"', '""', $item).'"';
            } elseif (str_contains($item, ',')) {
                $carry .= '"'.$item.'"';
            } else {
                $carry .= $item;
            }

            return $carry;
        });
    }

    protected function unserializeCsv(string $data): array
    {
        return str_getcsv($data);
    }
}
