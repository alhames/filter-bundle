<?php

namespace Alhames\FilterBundle\Filter;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Exception\InvalidConfigException;

class DateFilter extends AbstractFilter
{
    protected array $config = [
        'default' => null,
        'required' => false,
        'type' => 'date',
        'format' => 'Y-m-d',
        'min_date' => '1900-01-01',
        'max_date' => '2099-12-31',
    ];

    protected string $pattern = '(19|20)[0-9]{2}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])';

    public static function getType(): string
    {
        return 'date';
    }

    /**
     * @param \DateTimeInterface $value
     */
    protected function serialize(mixed $value, array $config): ?string
    {
        return $value->format('Y-m-d');
    }

    /**
     * @param string $value
     */
    protected function unserialize(mixed $value, array $config): string
    {
        return $value;
    }

    public function convertToResponse(mixed $value, array $config = []): ?string
    {
        return $this->normalize($value)?->format($config['format'] ?? $this->config['format']);
    }

    protected function normalize(mixed $value): ?\DateTimeInterface
    {
        if ($this->isNull($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable $exception) {
            return null; // todo
        }
    }

    protected function filterType(mixed $value, array $config): \DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (!is_string($value)) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config);
        }

        $config['pattern'] = $this->pattern;
        $value = $this->filterPattern($value, $config);

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable $exception) {
            throw FilterValueException::create(FilterValueException::TYPE_TYPE, $value, $config, $exception);
        }
    }

    protected function filterMinDate(\DateTimeInterface $value, array $config): \DateTimeInterface
    {
        $minDate = $this->normalize($config['min_date']);
        if (null === $minDate) {
            throw InvalidConfigException::create('min_date', $config['min_date'], $config);
        }
        if ($minDate > $value) {
            throw FilterValueException::create(FilterValueException::TYPE_MIN_DATE, $value, $config);
        }

        return $value;
    }

    protected function filterMaxDate(\DateTimeInterface $value, array $config): \DateTimeInterface
    {
        $maxDate = $this->normalize($config['max_date']);
        if (null === $maxDate) {
            throw InvalidConfigException::create('max_date', $config['max_date'], $config);
        }
        if ($maxDate < $value) {
            throw FilterValueException::create(FilterValueException::TYPE_MAX_DATE, $value, $config);
        }

        return $value;
    }
}
