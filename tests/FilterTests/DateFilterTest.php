<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\DateFilter;

class DateFilterTest extends AbstractFilterTestCase
{
    protected string $date = '1999-02-14';

    protected function getClass(): string
    {
        return DateFilter::class;
    }

    protected function getType(): string
    {
        return 'date';
    }

    protected function provideIsFile(): array
    {
        return [
            ['expected' => false],
        ];
    }

    protected function provideGetDefaultValue(): array
    {
        return [
            ['expected' => null],
            ['expected' => null, 'config' => ['default' => null]],
            ['expected' => null, 'config' => ['default' => '']],
        ];
    }

    public function testGetDefaultValueExtra(): void
    {
        $filter = $this->getFilter();
        $this->assertEquals(new \DateTimeImmutable($this->date), $filter->getDefaultValue(['default' => $this->date]));
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => $this->date, 'value' => new \DateTimeImmutable($this->date)],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
        ];
    }

    public function testConvertFromDbExtra(): void
    {
        $filter = $this->getFilter();
        $this->assertEquals(new \DateTimeImmutable($this->date), $filter->convertFromDb($this->date));
    }

    protected function provideConvertToResponse(): array
    {
        $date = new \DateTimeImmutable($this->date);

        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => $this->date, 'value' => $date],
            ['expected' => '14.02.1999', 'value' => $date, 'config' => ['format' => 'd.m.Y']],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
        ];
    }

    public function testFilterExtra(): void
    {
        $filter = $this->getFilter();
        $date = new \DateTimeImmutable($this->date);
        $this->assertEquals($date, $filter->filter($this->date));
        $this->assertSame($date, $filter->filter($date));
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
                'value' => '1990-01-01',
                'config' => ['min_date' => '2000-01-01'],
            ],
            [
                'exception_type' => FilterValueException::TYPE_MAX_DATE,
                'value' => '2010-01-01',
                'config' => ['max_date' => '2000-01-01'],
            ],
        ];
    }
}
