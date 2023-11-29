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
        return $this->createPriceFromAmount($this->amount * $by);
    }

    public function plus(PriceInterface $price): PriceInterface
    {
        return $this->createPriceFromAmount($this->amount + $price->getAmount());
    }

    public function minus(PriceInterface $price): PriceInterface
    {
        return $this->createPriceFromAmount($this->amount - $price->getAmount());
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

    public function toArray(): array
    {
        return [
            'amount_minor' => $this->amount,
            'currency' => $this->currency,
            'formatted_value' => $this->formattedValue,
        ];
    }

    private function createPriceFromAmount(int $amount): PriceInterface
    {
        $money = Money::ofMinor($amount, $this->currency);

        return new Price($amount, $this->currency, $money->formatTo('en'));
    }
}
