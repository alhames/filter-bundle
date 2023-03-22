<?php

namespace Alhames\FilterBundle\Exception;

interface FilterExceptionInterface extends \Throwable
{
    public function getType(): string;

    public function getValue(): mixed;

    public function getConfig(): array;

    public function getConfigPath(): string;

    public function setConfigPath(string $path): void;
}
