<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

use LML\SDK\Model\IdInterface;

interface ProductFaqInterface extends IdInterface
{
    public function getQuestion(): string;

    public function getAnswer(): string;

    public function getPriority(): int;
}
