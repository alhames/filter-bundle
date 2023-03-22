<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\DatetimeFilter;

class DatetimeFilterTest extends DateFilterTest
{
    protected string $date = '1999-02-14 12:31:28';

    protected function getClass(): string
    {
        return DatetimeFilter::class;
    }

    protected function getType(): string
    {
        return 'datetime';
    }

    protected function provideConvertToResponse(): array
    {
        $date = new \DateTimeImmutable($this->date);

        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => '1999-02-14T12:31:28+00:00', 'value' => $date],
            ['expected' => '14.02.1999, 12:31', 'value' => $date, 'config' => ['format' => 'd.m.Y, H:i']],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => []],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 3.14],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => false],

            ['exception_type' => FilterValueException::TYPE_PATTERN, 'value' => 'string'],

            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            [
                'exception_type' => FilterValueException::TYPE_MIN_DATE,
                'value' => '2000-01-01 06:00:00',
                'config' => ['min_date' => '2000-01-01 12:00:00'],
            ],
            [
                'exception_type' => FilterValueException::TYPE_MAX_DATE,
                'value' => '2000-01-01 18:00:00',
                'config' => ['max_date' => '2000-01-01 12:00:00'],
            ],
        ];
    }
}
