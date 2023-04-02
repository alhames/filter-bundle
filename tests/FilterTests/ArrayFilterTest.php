<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\ArrayFilter;

class ArrayFilterTest extends AbstractFilterTestCase
{
    private const COMPRESSED_STRING = 'H4sIAAAAAAACA0u0MrSqzrQysM60MrWuBQBAaa6vDgAAAA==';

    protected function getClass(): string
    {
        return ArrayFilter::class;
    }

    protected function getType(): string
    {
        return 'array';
    }

    protected function provideIsFile(): array
    {
        return [
            ['expected' => false],
            [
                'expected' => false,
                'config' => [
                    'items' => ['type' => 'integer'],
                ]
            ],
            [
                'expected' => true,
                'config' => [
                    'items' => ['type' => 'file'],
                ]
            ],
        ];
    }

    protected function provideGetDefaultValue(): array
    {
        return [
            ['expected' => null],
            ['expected' => null, 'config' => ['default' => null]],
            ['expected' => null, 'config' => ['default' => '']],
            ['expected' => null, 'config' => ['default' => []]],
            ['expected' => [1, 2, 3], 'config' => ['default' => [1, 2, 3]]],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => null, 'value' => []],
            ['expected' => 'a:1:{i:0;i:5;}', 'value' => ['level' => 5]],
            [
                'expected' => 'a:1:{i:0;i:5;}',
                'value' => ['level' => 5],
                'config' => [
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => 'a:1:{s:5:"level";i:5;}',
                'value' => ['level' => 5],
                'config' => [
                    'keys' => ['type' => 'string'],
                ],
            ],
            [
                'expected' => 'a:1:{s:5:"level";i:5;}',
                'value' => ['level' => 5],
                'config' => [
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],

            [
                'expected' => '{"level":5,"stage":2}',
                'value' => ['level' => "5", 'stage' => "2"],
                'config' => [
                    'format' => 'json',
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => '[5,2]',
                'value' => ['level' => "5", 'stage' => "2"],
                'config' => [
                    'format' => 'json',
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => '5,2',
                'value' => ['level' => "5", 'stage' => "2"],
                'config' => [
                    'format' => 'csv',
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => null,
                'value' => [],
                'config' => ['compress' => true],
            ],
            [
                'expected' => base64_decode(self::COMPRESSED_STRING),
                'value' => ['level' => 5],
                'config' => [
                    'compress' => true,
                    'items' => ['type' => 'integer'],
                ],
            ],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            [
                'expected' => [5],
                'value' => 'a:1:{s:5:"level";i:5;}',
            ],
            [
                'expected' => ['level' => 5],
                'value' => 'a:1:{s:5:"level";i:5;}',
                'config' => [
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => ['level' => 5],
                'value' => '{"level":5}',
                'config' => [
                    'format' => 'json',
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => [5],
                'value' => base64_decode(self::COMPRESSED_STRING),
                'config' => ['compress' => true],
            ],
            [
                'expected' => null,
                'value' => null,
                'config' => ['compress' => true],
            ],
        ];
    }

    protected function provideConvertToResponse(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => null, 'value' => []],
            ['expected' => [5], 'value' => ['level' => 5]],
            [
                'expected' => [5],
                'value' => ['level' => 5],
                'config' => [
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => ['level' => 5],
                'value' => ['level' => 5],
                'config' => [
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => [22 => 5],
                'value' => [22 => 5],
                'config' => [
                    'keys' => ['type' => 'integer'],
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => [5],
                'value' => [22 => 5],
                'config' => [
                    'items' => ['type' => 'integer'],
                ],
            ],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => [5], 'value' => ['level' => 5]],
            [
                'expected' => [5],
                'value' => ['level' => '5'],
                'config' => [
                    'items' => ['type' => 'integer'],
                ],
            ],
            [
                'expected' => ['level' => 5],
                'value' => ['level' => '5'],
                'config' => [
                    'keys' => ['type' => 'string'],
                    'items' => ['type' => 'integer'],
                ],
            ],
        ];
    }

    protected function provideFilterRequest(): array
    {
        $data = $this->provideFilter();
        $data[] = [
            'expected' => ['aaa' => ['ccc' => 10], 'bbb' => ['ccc' => 3]],
            'value' => [
                'aaa' => '{"ccc":"10"}',
                'bbb' => '{"ccc":"3"}',
            ],
            'config' => [
                'keys' => ['type' => 'string'],
                'items' => [
                    'type' => 'object',
                    'format' => 'json',
                    'properties' => [
                        'ccc' => ['type' => 'integer']
                    ],
                ],
            ],
        ];
        $data[] = [
            'expected' => [['ccc' => 10], ['ccc' => 3]],
            'value' => '[{"ccc":"10"},{"ccc":"3"}]',
            'config' => [
                'format' => 'json',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'ccc' => ['type' => 'integer']
                    ],
                ],
            ],
        ];

        return $data;
    }

    protected function provideFilterException(): array
    {
        return [
            'type_bool_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => true],
            'type_int_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 111],
            'type_string_exception' => ['exception_type' => FilterValueException::TYPE_TYPE, 'value' => 'str'],

            'required_null_exception' => ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => null, 'config' => ['required' => true]],
            'required_string_exception' => ['exception_type' => FilterValueException::TYPE_REQUIRED, 'value' => '', 'config' => ['required' => true]],

            'internal_min_exception' => [
                'exception_type' => FilterValueException::TYPE_MIN,
                'value' => ['aaa' => '25', 'bbb' => '15'],
                'config' => [
                    'items' => [
                        'type' => 'integer',
                        'min' => 20,
                    ],
                ],
            ],

            'min_items_exception' => [
                'exception_type' => FilterValueException::TYPE_MIN_ITEMS,
                'value' => [1, 2, 3],
                'config' => [
                    'items' => ['type' => 'integer'],
                    'min_items' => 5,
                ],
            ],
            'max_items_exception' => [
                'exception_type' => FilterValueException::TYPE_MAX_ITEMS,
                'value' => [1, 2, 3],
                'config' => [
                    'items' => ['type' => 'integer'],
                    'max_items' => 2,
                ],
            ],
        ];
    }
}
