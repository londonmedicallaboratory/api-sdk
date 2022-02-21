<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Money;

interface PriceInterface
{
    public function getAmount(): int;

    public function getCurrency(): string;

    public function getFormattedValue(): string;

    public function multiply(int $by): PriceInterface;
}
