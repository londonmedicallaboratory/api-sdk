<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Money;

use Brick\Money\Money;

class Price implements PriceInterface
{
    public function __construct(
        protected int $amount,
        protected string $currency,
        protected string $formattedValue,
    )
    {
    }

    public static function fromMoney(Money $money): Price
    {
        return new Price($money->getMinorAmount()->toInt(), $money->getCurrency()->__toString(), $money->formatTo('en'));
    }

    public function __toString(): string
    {
        return $this->formattedValue;
    }

    public function multiply(int $by): PriceInterface
    {
        $newAmount = $this->amount * $by;
        $money = Money::ofMinor($newAmount, $this->currency);

        return new Price($newAmount, $this->currency, $money->formatTo('en'));
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
