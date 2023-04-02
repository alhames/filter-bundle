<?php

namespace Alhames\FilterBundle\Tests\FilterTests;

use Alhames\FilterBundle\Exception\FilterValueException;
use Alhames\FilterBundle\Filter\ObjectFilter;

class ObjectFilterTest extends AbstractFilterTestCase
{
    private const COMPRESSED_STRING = 'H4sIAAAAAAACA0u0MrSqLrYytVLKSS1LzVGyzrQyta4FAB+3LA8WAAAA';

    protected function getClass(): string
    {
        return ObjectFilter::class;
    }

    protected function getType(): string
    {
        return 'object';
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
            ['expected' => null, 'config' => ['default' => []]],
            ['expected' => ['level' => 5], 'config' => ['default' => ['level' => 5]]],
        ];
    }

    protected function provideConvertToDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => null, 'value' => []],
            ['expected' => 'a:1:{s:5:"level";i:5;}', 'value' => ['level' => 5]],
            [
                'expected' => '{"level":5}',
                'value' => ['level' => 5],
                'config' => ['format' => 'json'],
            ],
            [
                'expected' => null,
                'value' => [],
                'config' => ['compress' => true],
            ],
            [
                'expected' => base64_decode(self::COMPRESSED_STRING),
                'value' => ['level' => 5],
                'config' => ['compress' => true],
            ],
        ];
    }

    protected function provideConvertFromDb(): array
    {
        return [
            ['expected' => null, 'value' => null],
            [
                'expected' => ['level' => 5],
                'value' => 'a:1:{s:5:"level";i:5;}',
            ],
            [
                'expected' => ['level' => 5],
                'value' => '{"level":5}',
                'config' => ['format' => 'json'],
            ],
            [
                'expected' => ['level' => 5],
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
            ['expected' => ['level' => 5], 'value' => ['level' => 5]],
        ];
    }

    protected function provideFilter(): array
    {
        return [
            ['expected' => null, 'value' => null],
            ['expected' => null, 'value' => ''],
            ['expected' => ['level' => 5], 'value' => ['level' => 5]],
            ['expected' => ['level' => '5'], 'value' => ['level' => '5']],
            [
                'expected' => ['level' => 5],
                'value' => [
                    'level' => '5',
                    'name' => 'John',
                ],
                'config' => [
                    'properties' => [
                        'level' => ['type' => 'integer'],
                    ],
                ],
            ],
        ];
    }

    protected function provideFilterRequest(): array
    {
        $data = $this->provideFilter();
        $data[] = [
            'expected' => ['aaa' => 5, 'bbb' => ['ccc' => 3]],
            'value' => [
                'aaa' => '5',
                'bbb' => '{"ccc":"3"}',
            ],
            'config' => [
                'properties' => [
                    'aaa' => ['type' => 'integer'],
                    'bbb' => [
                        'type' => 'object',
                        'properties' => [
                            'ccc' => ['type' => 'integer']
                        ],
                        'format' => 'json',
                    ]
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
                'value' => ['level' => '5', 'name' => 'John'],
                'config' => [
                    'properties' => [
                        'level' => ['type' => 'integer', 'min' => 10],
                        'name' => ['type' => 'string'],
                    ],
                ],
                'property' => 'level',
            ],

            'type_format_exception' => [
                'exception_type' => FilterValueException::TYPE_TYPE,
                'value' => [
                    'aaa' => '5',
                    'bbb' => '{"ccc":"3"}',
                ],
                'config' => [
                    'properties' => [
                        'aaa' => ['type' => 'integer'],
                        'bbb' => [
                            'type' => 'object',
                            'properties' => [
                                'ccc' => ['type' => 'integer']
                            ],
                            'format' => 'json',
                        ]
                    ],
                ],
                'property' => 'bbb',
            ],
            'internal_type_exception' => [
                'exception_type' => FilterValueException::TYPE_TYPE,
                'value' => [
                    'aaa' => '5',
                    'bbb' => ['ccc' => 'str'],
                ],
                'config' => [
                    'properties' => [
                        'aaa' => ['type' => 'integer'],
                        'bbb' => [
                            'type' => 'object',
                            'properties' => [
                                'ccc' => ['type' => 'integer']
                            ],
                        ]
                    ],
                ],
                'property' => 'bbb.ccc',
            ]
        ];
    }
}
