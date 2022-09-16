<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Product\ProductInterface;

/**
 * @psalm-type S=array{
 *      product_id: string,
 *      product_sku: string,
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
