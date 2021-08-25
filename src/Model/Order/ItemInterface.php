<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Product\ProductInterface;

interface ItemInterface
{
    public function getProduct(): ProductInterface;

    public function getQuantity(): int;

    public function getTotal(): PriceInterface;

    public function setQuantity(int $quantity): void;
}
