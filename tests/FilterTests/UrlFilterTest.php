<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\UrlFilter;

class UrlFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return UrlFilter::class;
    }

    protected function getType(): string
    {
        return 'url';
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
            ['expected' => 'example.com', 'config' => ['default' => 'example.com']],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'example.com', 'value' => 'example.com'],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'example.com', 'value' => 'example.com'],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'example.com', 'value' => 'example.com'],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            'null'          => ['expected' => null,                         'value' => null],
            'empty_string'  => ['expected' => null,                         'value' => ''],
            'domain'        => ['expected' => 'example.com',                'value' => 'example.com'],
            'trim'          => ['expected' => 'example.com',                'value' => '  example.com  '],
            'url'           => ['expected' => 'https://example.com/about',  'value' => 'https://example.com/about'],
            'default_null' => [
                'expected' => 'example.com',
                'value' => null,
                'config' => ['default' => 'example.com'],
            ],
            'default_empty_string' => [
                'expected' => 'example.com',
                'value' => '',
                'config' => ['default' => 'example.com'],
            ],
            'scheme' => [
                'expected' => 'https://example.com/about',
                'value' => 'https://example.com/about',
                'config' => ['scheme' => true],
            ],
            'pattern' => [
                'expected' => 'example.com/about',
                'value' => 'example.com/about',
                'config' => ['pattern' => '[-a-z0-9]+\.com(/.*)?'],
            ],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            'type_stdclass_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \stdClass()],
            'type_datetime_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],
            'type_string_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'string'],
            'type_scheme_exception' => [
                'exception_type' => FilterValueException::TYPE_TYPE,
                'value' => 'example.com/about',
                'config' => ['scheme' => true],
            ],
            'required_null_exception' => [
                'exception_type' => FilterValueException::TYPE_REQUIRED,
                'value' => null,
                'config' => ['required' => true],
            ],
            'required_string_exception' => [
                'exception_type' => FilterValueException::TYPE_REQUIRED,
                'value' => '',
                'config' => ['required' => true],
            ],
            'enum_exception' => [
                'exception_type' => FilterValueException::TYPE_ENUM,
                'value' => 'example.org',
                'config' => ['enum' => ['example.com', 'example.net']],
            ],
            'min_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MIN_LENGTH,
                'value' => 'a.eu',
                'config' => ['min_length' => 5],
            ],
            'max_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_LENGTH,
                'value' => 'example.com',
                'config' => ['max_length' => 8],
            ],
            'length_exception' => [
                'exception_type' => FilterValueException::TYPE_LENGTH,
                'value' => 'example.com',
                'config' => ['length' => 8],
            ],
            'pattern_exception' => [
                'exception_type' => FilterValueException::TYPE_PATTERN,
                'value' => 'example.net',
                'config' => ['pattern' => '[-a-z0-9]+\.com(/.*)?'],
            ],
            'max_string_size_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_STRING_SIZE,
                'value' => 'example.com',
                'config' => ['max_string_size' => 8],
            ],
        ];
    }
}
