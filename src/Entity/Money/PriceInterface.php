<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Money;

interface PriceInterface
{
    public function getAmount(): int;

    public function getCurrency(): string;

    public function getFormattedValue(): string;

    public function multiply(int $by): PriceInterface;

    public function plus(PriceInterface $price): PriceInterface;

    public function minus(PriceInterface $price): PriceInterface;

    /**
     * @return array{amount_minor: int, currency: string, formatted_value: string}
     */
    public function toArray(): array;
}
