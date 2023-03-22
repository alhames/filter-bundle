<?php

namespace Alhames\FilterBundle\Exception;

trait FilterExceptionTrait
{
    protected string $type;
    protected mixed $value;
    protected array $config;
    protected string $configPath = 'unknown';

    public static function create(string $type, mixed $value, array $config, ?\Throwable $previous = null): static
    {
        return new static($type, $value, $config, $previous);
    }

    public function __construct(string $type, mixed $value, array $config, ?\Throwable $previous = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->config = $config;

        parent::__construct(sprintf(self::MESSAGE_TEMPLATE, $this->type, $this->configPath), 0, $previous);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function setConfigPath(string $path): void
    {
        $this->configPath = $path;
        $this->message = sprintf(self::MESSAGE_TEMPLATE, $this->type, $this->configPath);
    }
}
