<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\IpFilter;

class IpFilterTest extends AbstractFilterTestCase
{
    private const IP = 2130706433; // 127.0.0.1

    protected function getClass(): string
    {
        return IpFilter::class;
    }

    protected function getType(): string
    {
        return 'ip';
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
            ['expected' => '127.0.0.1', 'config' => ['default' => '127.0.0.1']],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => '127.0.0.1', 'value' => self::IP],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => self::IP, 'value' => '127.0.0.1'],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => '127.0.0.1', 'value' => '127.0.0.1'],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => '127.0.0.1', 'value' => '127.0.0.1'],
            ['expected' => '127.0.0.1', 'value' => null, 'config' => ['default' => '127.0.0.1']],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => true],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 1],
            ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],

            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            ['exception_type' => FilterValueException::TYPE_PATTERN, 'value' => '127.0.0'],
            [
                'exception_type' => FilterValueException::TYPE_PATTERN,
                'value' => '127.0.0.1',
                'config' => ['pattern' => '192\.168\.\d{1,3}\.\d{1,3}'],
            ],

            [
                'exception_type' => FilterValueException::TYPE_ENUM,
                'value' => '127.0.0.9',
                'config' => ['enum' => ['127.0.0.1', '127.0.0.2']],
            ],
        ];
    }
}
