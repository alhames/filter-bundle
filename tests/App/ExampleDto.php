<?php

namespace Alhames\EntityBundle\Tests\App;

class ExampleDto
{
    public string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function __toString(): string
    {
        return 'Example: '.$this->text;
    }
}
