<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\StringFilter;

class StringFilterTest extends AbstractFilterTestCase
{
    private const COMPRESSED_STRING = 'H4sIAAAAAAACA6vKLAAARpUdQgMAAAA=';

    protected function getClass(): string
    {
        return StringFilter::class;
    }

    protected function getType(): string
    {
        return 'string';
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
            ['expected' => 'word', 'config' => ['default' => 'word']],
            ['expected' => '-9', 'config' => ['default' => -9]],
            ['expected' => '22.2', 'config' => ['default' => 22.2]],
            ['expected' => '1', 'config' => ['default' => true]],
            ['expected' => '0', 'config' => ['default' => false]],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'word', 'value' => 'word'],
            ['expected' => '-9', 'value' => -9],
            ['expected' => '22.2', 'value' => 22.2],
            ['expected' => '1', 'value' => true],
            ['expected' => '0', 'value' => false],
            [
                'expected' => null,
                'value' => '',
                'config' => ['compress' => true],
            ],
            [
                'expected' => base64_decode(self::COMPRESSED_STRING),
                'value' => 'zip',
                'config' => ['compress' => true],
            ],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'word', 'value' => 'word'],
            ['expected' => '-9', 'value' => -9],
            ['expected' => '22.2', 'value' => 22.2],
            ['expected' => '1', 'value' => true],
            ['expected' => '0', 'value' => false],
            [
                'expected' => 'zip',
                'value' => base64_decode(self::COMPRESSED_STRING),
                'config' => ['compress' => true],
            ],
            [
                'expected' => null,
                'value' => '',
                'config' => ['compress' => true],
            ],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'word', 'value' => 'word'],
            ['expected' => '-9', 'value' => -9],
            ['expected' => '22.2', 'value' => 22.2],
            ['expected' => '1', 'value' => true],
            ['expected' => '0', 'value' => false],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => 'word', 'value' => 'word'],
            ['expected' => '-9', 'value' => -9],
            ['expected' => '22.2', 'value' => 22.2],
            ['expected' => '1', 'value' => true],
            ['expected' => '0', 'value' => false],

            ['expected' => 'word', 'value' => null, 'config' => ['default' => 'word']],
            ['expected' => 'word', 'value' => '', 'config' => ['default' => 'word']],

            ['expected' => 'word', 'value' => '  word  '],
            ['expected' => '  word  ', 'value' => '  word  ', 'config' => ['trim' => false]],

            ['expected' => 'abc', 'value' => 'abc', 'config' => ['pattern' => '[a-c]+']],
        ];
    }

    protected function provideFilterException(): array
    {
        return [
            'type_stdclass_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \stdClass()],
            'type_datetime_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => new \DateTime()],

            'required_null_exception' => ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            'required_string_exception' => ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            'enum_exception' => [
                'exception_type' => FilterValueException::TYPE_ENUM,
                'value' => 'banana',
                'config' => ['enum' => ['orange', 'melon']],
            ],
            'min_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MIN_LENGTH,
                'value' => 'word',
                'config' => ['min_length' => 5],
            ],
            'max_length_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_LENGTH,
                'value' => 'word',
                'config' => ['max_length' => 3],
            ],
            'length_exception' => [
                'exception_type' => FilterValueException::TYPE_LENGTH,
                'value' => 'word',
                'config' => ['length' => 3],
            ],
            'pattern_exception' => [
                'exception_type' => FilterValueException::TYPE_PATTERN,
                'value' => 'word',
                'config' => ['pattern' => '[a-c]+'],
            ],
            'max_string_size_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_STRING_SIZE,
                'value' => 'абв',
                'config' => ['max_string_size' => 3],
            ],
        ];
    }
}
