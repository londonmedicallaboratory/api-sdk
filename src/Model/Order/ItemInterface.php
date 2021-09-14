<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Product\ProductInterface;

/**
 * @psalm-type S=array{
 *      product_id: string,
 *      quantity: int,
 * }
 *
 * @extends ModelInterface<S>
 */
interface ItemInterface extends ModelInterface
{
    public function getProduct(): ProductInterface;

    public function getQuantity(): int;

    public function getTotal(): PriceInterface;

    public function setQuantity(int $quantity): void;
}
