<?php

declare(strict_types=1);

namespace App\Model\Basket;

use App\Model\Money\PriceInterface;

interface BasketInterface
{
    /**
     * @return iterable<BasketItemInterface>
     */
    public function getItems(): iterable;

    public function getTotal(): PriceInterface;
}
