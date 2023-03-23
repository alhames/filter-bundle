<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\EmailFilter;

class EmailFilterTest extends AbstractFilterTestCase
{
    protected function getClass(): string
    {
        return EmailFilter::class;
    }

    protected function getType(): string
    {
        return 'email';
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
            ['expected' => 'mail@example.com', 'config' => ['default' => 'mail@example.com']],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'mail@example.com', 'value' => 'mail@example.com'],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'mail@example.com', 'value' => 'mail@example.com'],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'mail@example.com', 'value' => 'mail@example.com'],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            'null'          => ['expected' => null,                 'value' => null],
            'empty_string'  => ['expected' => null,                 'value' => ''],
            'email'         => ['expected' => 'mail@example.com',   'value' => 'mail@example.com'],
            'trim'          => ['expected' => 'mail@example.com',   'value' => '  mail@example.com  '],
            'default_null' => [
                'expected' => 'mail@example.com',
                'value' => null,
                'config' => ['default' => 'mail@example.com'],
            ],
            'default_empty_string' => [
                'expected' => 'mail@example.com',
                'value' => '',
                'config' => ['default' => 'mail@example.com'],
            ],
            'pattern' => [
                'expected' => 'mail@example.com',
                'value' => 'mail@example.com',
                'config' => ['pattern' => '[-a-z0-9]+@example\.com'],
            ],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            'type_stdclass_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \stdClass()],
            'type_datetime_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],
            'type_string_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'string'],
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
                'value' => 'mail@example.org',
                'config' => ['enum' => ['mail@example.com', 'mail@example.net']],
            ],
            'min_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MIN_LENGTH,
                'value' => 'mail@example.com',
                'config' => ['min_length' => 30],
            ],
            'max_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_LENGTH,
                'value' => 'mail@example.com',
                'config' => ['max_length' => 10],
            ],
            'length_exception' => [
                'exception_type' => FilterValueException::TYPE_LENGTH,
                'value' => 'mail@example.com',
                'config' => ['length' => 10],
            ],
            'pattern_exception' => [
                'exception_type' => FilterValueException::TYPE_PATTERN,
                'value' => 'mail@example.net',
                'config' => ['pattern' => '[-a-z0-9]+@example\.com'],
            ],
            'max_string_size_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_STRING_SIZE,
                'value' => 'mail@example.com',
                'config' => ['max_string_size' => 10],
            ],
        ];
    }
}
