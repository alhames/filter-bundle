<?php

namespace Alhames\FilterBundle\Exception;

class ConvertException extends \RuntimeException implements FilterExceptionInterface
{
    use FilterExceptionTrait;

    public const TYPE_COMPRESS = 'compress';

    protected const MESSAGE_TEMPLATE = 'Convertation error "%s" in "%s".';

    private ?string $method = null;

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): static
    {
        $this->method = $method;

        return $this;
    }
}
