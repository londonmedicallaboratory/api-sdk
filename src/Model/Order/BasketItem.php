<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Product\ProductInterface;

class BasketItem implements ItemInterface
{
    public function __construct(
        private ProductInterface $product,
        private int              $quantity,
    )
    {
    }

    public function getTotal(): PriceInterface
    {
        return $this->getProduct()->getPrice()->multiply($this->getQuantity());
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            $quantity = 0;
        }
        $this->quantity = $quantity;
    }

    public function toArray()
    {
        return [
            'product_id' => $this->getProduct()->getId(),
            'quantity' => $this->getQuantity(),
        ];
    }

    public function getId(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->getProduct()->getName();
    }
}
