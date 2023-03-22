<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\FloatFilter;

class FloatFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return FloatFilter::class;
    }

    protected function getType(): string
    {
        return 'float';
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

            ['expected' => 2.0, 'config' => ['default' => 2]],
            ['expected' => 2.0, 'config' => ['default' => 2.0]],
            ['expected' => 2.0, 'config' => ['default' => '2']],
            ['expected' => 2.0, 'config' => ['default' => '2.0']],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0.0, 'value' => 0],
            ['expected' => 2.0, 'value' => 2],
            ['expected' => 2.0, 'value' => '2'],
            ['expected' => 2.33, 'value' => 2.33],
            ['expected' => 2.33, 'value' => '2.33'],
            ['expected' => -0.5, 'value' => -0.5],
            ['expected' => -0.5, 'value' => '-0.5'],
            ['expected' => -9.0, 'value' => -9],

            ['expected' => 3_533, 'value' => 106 / 3, 'config' => ['format' => 'integer']],
            ['expected' => 987_654, 'value' => 98.7654321, 'config' => ['format' => 'integer', 'precision' => 4]],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0.0, 'value' => 0],
            ['expected' => 2.0, 'value' => 2],
            ['expected' => 2.0, 'value' => '2'],
            ['expected' => 2.33, 'value' => 2.33],
            ['expected' => 2.33, 'value' => '2.33'],
            ['expected' => -0.5, 'value' => -0.5],
            ['expected' => -0.5, 'value' => '-0.5'],
            ['expected' => -9.0, 'value' => -9],

            ['expected' => 123.45, 'value' => 12345, 'config' => ['format' => 'integer']],
            ['expected' => 1.2345, 'value' => 12345, 'config' => ['format' => 'integer', 'precision' => 4]],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0.0, 'value' => 0],
            ['expected' => 2.0, 'value' => 2],
            ['expected' => 2.0, 'value' => '2'],
            ['expected' => 2.33, 'value' => 2.33],
            ['expected' => 2.33, 'value' => '2.33'],
            ['expected' => -0.5, 'value' => -0.5],
            ['expected' => -0.5, 'value' => '-0.5'],
            ['expected' => -9.0, 'value' => -9],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0.0, 'value' => null, 'config' => ['default' => 0.0]],
            ['expected' => 99.99, 'value' => null, 'config' => ['default' => 99.99]],
            ['expected' => -0.5, 'value' => null, 'config' => ['default' => -0.5]],
            ['expected' => 0.0, 'value' => 0],
            ['expected' => 2.0, 'value' => 2],
            ['expected' => 2.0, 'value' => '2'],
            ['expected' => 2.33, 'value' => 2.33],
            ['expected' => 2.33, 'value' => '2.33'],
            ['expected' => -0.5, 'value' => -0.5, 'config' => ['min' => -1000]],
            ['expected' => -0.5, 'value' => '-0.5', 'config' => ['min' => -1000]],
            ['expected' => -9.0, 'value' => -9, 'config' => ['min' => -1000]],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => []],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => true],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'string'],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => '0x1'],

            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            ['exception_type' => FilterValueException::TYPE_ENUM, 'value' => 1.5, 'config' => ['enum' => [1.0, 2.0]]],

            ['exception_type' => FilterValueException::TYPE_MIN, 'value' => 1.5, 'config' => ['min' => 2.0]],

            ['exception_type' => FilterValueException::TYPE_MAX, 'value' => 1.5, 'config' => ['max' => 1.0]],
        ];
    }
}
