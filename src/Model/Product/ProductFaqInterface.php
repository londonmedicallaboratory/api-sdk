<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Model\IdInterface;

interface ProductFaqInterface extends IdInterface
{
    public function getQuestion(): string;

    public function getAnswer(): string;

    public function getPriority(): int;
}
