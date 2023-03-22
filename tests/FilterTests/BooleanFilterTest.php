<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\BooleanFilter;

class BooleanFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return BooleanFilter::class;
    }

    protected function getType(): string
    {
        return 'boolean';
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
            ['expected' => true, 'config' => ['default' => 1]],
            ['expected' => true, 'config' => ['default' => true]],
            ['expected' => true, 'config' => ['default' => '1']],
            ['expected' => false, 'config' => ['default' => 0]],
            ['expected' => false, 'config' => ['default' => false]],
            ['expected' => false, 'config' => ['default' => '0']],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 1, 'value' => 1],
            ['expected' => 1, 'value' => true],
            ['expected' => 1, 'value' => '1'],
            ['expected' => 0, 'value' => 0],
            ['expected' => 0, 'value' => false],
            ['expected' => 0, 'value' => '0'],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => true, 'value' => 1],
            ['expected' => true, 'value' => true],
            ['expected' => true, 'value' => '1'],
            ['expected' => false, 'value' => 0],
            ['expected' => false, 'value' => false],
            ['expected' => false, 'value' => '0'],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => true, 'value' => 1],
            ['expected' => true, 'value' => true],
            ['expected' => true, 'value' => '1'],
            ['expected' => false, 'value' => 0],
            ['expected' => false, 'value' => false],
            ['expected' => false, 'value' => '0'],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => true, 'value' => null, 'config' => ['default' => true]],
            ['expected' => true, 'value' => 1],
            ['expected' => true, 'value' => true],
            ['expected' => true, 'value' => '1'],
            ['expected' => false, 'value' => 0],
            ['expected' => false, 'value' => false],
            ['expected' => false, 'value' => '0'],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => []],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'string'],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 3.14],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 9],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'yes'],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'false'],

            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],
        ];
    }
}
