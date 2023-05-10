<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Product\Product;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Money\PriceInterface;

/**
 * @template TProduct of Product
 *
 * @psalm-type S=array{
 *      product_id: string,
 *      product_sku: string,
 *      quantity: int,
 * }
 *
 * @implements ModelInterface<S>
 */
class OrderItem implements ModelInterface
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

    public function __toString(): string
    {
        return $this->getProduct()->getName();
    }

    public function getId(): string
    {
        return $this->__toString();
    }

    public function getTotal(): PriceInterface
    {
        return $this->getProduct()->getPrice()->multiply($this->getQuantity());
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
        $this->quantity = max(0, $quantity);
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->getProduct()->getId(),
            'product_sku' => $this->getProduct()->getSku(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
