<?php

namespace Alhames\FilterBundle\Exception;

class FilterValueException extends \RuntimeException implements FilterExceptionInterface
{
    use FilterExceptionTrait;

    public const TYPE_TYPE = 'type';
    public const TYPE_REQUIRED = 'required';
    public const TYPE_ENUM = 'enum';
    public const TYPE_MIN = 'min';
    public const TYPE_MAX = 'max';
    public const TYPE_PATTERN = 'pattern';
    public const TYPE_LENGTH = 'length';
    public const TYPE_MIN_LENGTH = 'min_length';
    public const TYPE_MAX_LENGTH = 'max_length';
    public const TYPE_MIN_DATE = 'min_date';
    public const TYPE_MAX_DATE = 'max_date';
    public const TYPE_MAX_STRING_SIZE = 'max_size';
    public const TYPE_MAX_FILE_SIZE = 'max_file_size';
    public const TYPE_FILE = 'file';
    public const TYPE_UPLOAD = 'upload';

    public const TRANSLATION_DOMAIN = 'filter_errors';

    protected const MESSAGE_TEMPLATE = 'Error "%s" in "%s".';

    public function getParameters(): array
    {
        return [];
    }
}
