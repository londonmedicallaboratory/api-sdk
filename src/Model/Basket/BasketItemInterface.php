<?php

declare(strict_types=1);

namespace LML\SDK\Model\Basket;

use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Product\ProductInterface;

interface BasketItemInterface
{
    public function getProduct(): ProductInterface;

    public function getQuantity(): int;

    public function getTotal(): PriceInterface;
}
