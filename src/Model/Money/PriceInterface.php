<?php

declare(strict_types=1);

namespace App\Model\Money;

use Stringable;

interface PriceInterface extends Stringable
{
    public function getAmount(): int;

    public function getCurrency(): string;

    public function getCurrencyCode(): string;
}
