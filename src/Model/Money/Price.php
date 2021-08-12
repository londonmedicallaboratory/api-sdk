<?php

declare(strict_types=1);

namespace LML\SDK\Model\Money;

class Price implements PriceInterface
{
    public function __construct(
        protected int $amount,
        protected string $currency,
        protected string $formattedValue,
    )
    {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getFormattedValue(): string
    {
        return $this->formattedValue;
    }
}
