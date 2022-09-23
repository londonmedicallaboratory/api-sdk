<?php

declare(strict_types=1);

namespace LML\SDK\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Page
{
    public function __construct(
        private string $name = 'page',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
