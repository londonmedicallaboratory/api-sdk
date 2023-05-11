<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Basket;

use LML\SDK\Entity\Product\Product;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Money\PriceInterface;

/**
 * @template TProduct of Product
 */
class BasketItem
{
    /**
     * @param LazyValueInterface<TProduct> $product
     */
    public function __construct(
        private LazyValueInterface $product,
        private int $quantity,
    )
    {
    }

    /**
     * @return TProduct
     */
    public function getProduct(): Product
    {
        return $this->product->getValue();
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getTotal(): PriceInterface
    {
        return $this->getProduct()->getPrice()->multiply($this->getQuantity());
    }
}
