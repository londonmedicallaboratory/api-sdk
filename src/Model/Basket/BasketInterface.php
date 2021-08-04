<?php

declare(strict_types=1);

namespace LML\SDK\Model\Basket;

use LML\SDK\Model\Money\PriceInterface;

interface BasketInterface
{
    /**
     * @return iterable<BasketItemInterface>
     */
    public function getItems(): iterable;

    public function getTotal(): PriceInterface;
}
