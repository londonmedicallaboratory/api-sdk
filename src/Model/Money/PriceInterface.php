<?php

declare(strict_types=1);

namespace LML\SDK\Model\Money;

interface PriceInterface
{
    public function getAmount(): int;

    public function getCurrency(): string;

    public function getCurrencyCode(): string;

    public function getFormattedValue(): string;
}
