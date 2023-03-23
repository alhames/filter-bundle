<?php

namespace Alhames\FilterBundle\Filter;

interface FilterInterface
{
    public static function getType(): string;

    public function isFile(array $config = []): bool;

    public function getDefaultValue(array $config = []): mixed;

    public function convert(mixed $value, array $config = []): mixed;

    public function convertToDb(mixed $value, array $config = []): mixed;

    public function convertFromDb(mixed $value, array $config = []): mixed;

    public function convertToResponse(mixed $value, array $config = []): mixed;

    public function filter(mixed $value, array $config = []): mixed;

    public function filterRequest(mixed $value, array $config = []): mixed;
}
