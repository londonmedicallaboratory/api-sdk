<?php

declare(strict_types=1);

namespace App\Model\Basket;

use App\Model\Money\PriceInterface;
use App\Model\Product\ProductInterface;

interface BasketItemInterface
{
    public function getProduct(): ProductInterface;

    public function getQuantity(): int;

    public function getTotal(): PriceInterface;
}
