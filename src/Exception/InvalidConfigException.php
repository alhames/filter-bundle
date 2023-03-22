<?php

namespace Alhames\FilterBundle\Exception;

class InvalidConfigException extends \InvalidArgumentException implements FilterExceptionInterface
{
    use FilterExceptionTrait;

    protected const MESSAGE_TEMPLATE = 'Invalid configuration "%s" in "%s".';

    protected string $type;
    protected mixed $value;
    protected array $config;
    protected string $configPath = 'unknown';
}
