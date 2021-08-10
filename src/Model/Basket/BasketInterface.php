<?php

declare(strict_types=1);

namespace LML\SDK\Model\Basket;

use LML\SDK\Model\Money\PriceInterface;

interface BasketInterface
{
    /**
     * @return list<BasketItemInterface>
     */
    public function getItems();

    public function getTotal(): PriceInterface;
}
