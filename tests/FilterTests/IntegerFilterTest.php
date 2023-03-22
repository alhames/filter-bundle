<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\IntegerFilter;

class IntegerFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return IntegerFilter::class;
    }

    protected function getType(): string
    {
        return 'integer';
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
            ['expected' => 0, 'config' => ['default' => 0]],
            ['expected' => 0, 'config' => ['default' => '0']],
            ['expected' => 0, 'config' => ['default' => 0.0]],
            ['expected' => 0, 'config' => ['default' => '0.0']],
            ['expected' => 1, 'config' => ['default' => 1]],
            ['expected' => 1, 'config' => ['default' => '1']],
            ['expected' => 1, 'config' => ['default' => 1.0]],
            ['expected' => 1, 'config' => ['default' => '1.0']],
            ['expected' => 1, 'config' => ['default' => 1.8]],
            ['expected' => -999, 'config' => ['default' => -999]],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0, 'value' => 0],
            ['expected' => 0, 'value' => '0'],
            ['expected' => 0, 'value' => 0.0],
            ['expected' => 0, 'value' => '0.0'],
            ['expected' => 1, 'value' => 1],
            ['expected' => 1, 'value' => '1'],
            ['expected' => 1, 'value' => 1.0],
            ['expected' => 1, 'value' => '1.0'],
            ['expected' => 1, 'value' => 1.8],
            ['expected' => -999, 'value' => -999],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0, 'value' => 0],
            ['expected' => 0, 'value' => '0'],
            ['expected' => 0, 'value' => 0.0],
            ['expected' => 0, 'value' => '0.0'],
            ['expected' => 1, 'value' => 1],
            ['expected' => 1, 'value' => '1'],
            ['expected' => 1, 'value' => 1.0],
            ['expected' => 1, 'value' => '1.0'],
            ['expected' => 1, 'value' => 1.8],
            ['expected' => -999, 'value' => -999],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0, 'value' => 0],
            ['expected' => 0, 'value' => '0'],
            ['expected' => 0, 'value' => 0.0],
            ['expected' => 0, 'value' => '0.0'],
            ['expected' => 1, 'value' => 1],
            ['expected' => 1, 'value' => '1'],
            ['expected' => 1, 'value' => 1.0],
            ['expected' => 1, 'value' => '1.0'],
            ['expected' => 1, 'value' => 1.8],
            ['expected' => -999, 'value' => -999],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 0, 'value' => 0],
            ['expected' => 0, 'value' => '0'],
            ['expected' => 0, 'value' => 0.0],
            ['expected' => 0, 'value' => '0.0'],
            ['expected' => 1, 'value' => 1],
            ['expected' => 1, 'value' => '1'],
            ['expected' => 1, 'value' => 1.0],
            ['expected' => 1, 'value' => '1.0'],
            ['expected' => 1, 'value' => 1.8],
            ['expected' => -999, 'value' => -999, 'config' => ['min' => -1000_000]],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => []],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => true],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'string'],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => '0x1'],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],

            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            ['exception_type' => FilterValueException::TYPE_ENUM, 'value' => 15, 'config' => ['enum' => [10, 20]]],

            ['exception_type' => FilterValueException::TYPE_MIN, 'value' => 15, 'config' => ['min' => 20]],
            ['exception_type' => FilterValueException::TYPE_MAX, 'value' => 15, 'config' => ['max' => 10]],
        ];
    }
}
