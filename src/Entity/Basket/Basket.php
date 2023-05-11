<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Basket;

use RuntimeException;
use Brick\Money\Money;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Voucher\Voucher;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Repository\Basket\BasketRepository;
use function array_map;
use function array_reduce;

/**
 * @psalm-import-type S from Address as TAddress
 *
 * @psalm-type S = array{
 *     id: ?string,
 *     voucher_id: ?string,
 *     shipping_id: ?string,
 *     items: list<array{product_id: string, quantity: int}>,
 *     shipping_address?: ?TAddress,
 *     initial_appointment?: array{
 *         brand_id: string,
 *         appointment_time: string,
 *     },
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: BasketRepository::class, baseUrl: 'basket')]
class Basket implements ModelInterface
{
    /**
     * @param list<BasketItem> $items
     */
    public function __construct(
        private ?Address $shippingAddress = null,
        private array $items = [],
        private ?Voucher $voucher = null,
        private ?Shipping $shipping = null,
        private ?Appointment $initialAppointment = null,
        private ?string $id = null,
    )
    {
    }

    public function toArray()
    {
        $data = [
            'id' => $this->id,
            'voucher_id' => $this->getVoucher()?->getId(),
            'shipping_id' => $this->getShipping()?->getId(),
            'items' => array_map(fn(BasketItem $item) => [
                'product_id' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
            ], $this->getItems()),
        ];

        if ($shippingAddress = $this->shippingAddress) {
            $data['shipping_address'] = [
                'line1' => $shippingAddress->getLine1(),
                'line2' => $shippingAddress->getLine2(),
                'line3' => $shippingAddress->getLine3(),
                'postal_code' => $shippingAddress->getPostalCode(),
                'country_code' => $shippingAddress->getCountryCode(),
                'city' => $shippingAddress->getCity(),
            ];
        }
        if ($initialAppointmentTime = $this->initialAppointment) {
            $data['initial_appointment'] = [
                'brand_id' => $initialAppointmentTime->getBrand()->getId(),
                'appointment_time' => $initialAppointmentTime->getAppointmentTime()->format('Y-m-d\TH:i:sP'),
            ];
        }

        return $data;
    }

    public function getVoucher(): ?Voucher
    {
        return $this->voucher;
    }

    public function setVoucher(?Voucher $voucher): void
    {
        $this->voucher = $voucher;
    }

    public function getSubtotal(): ?PriceInterface
    {
        $total = array_reduce($this->getItems(), fn(int $carry, BasketItem $item) => $item->getTotal()->getAmount() + $carry, 0);
        if (!$total) {
            return null;
        }

        return Price::fromMoney(Money::ofMinor($total, 'GBP'));
    }

    public function getTotal(): ?PriceInterface
    {
        if (!$subtotal = $this->getSubtotal()) {
            return null;
        }
        $newPrice = $this->applyVoucher($subtotal);

        return $this->getShipping() ? $newPrice->plus($this->getShipping()->getPrice()) : $newPrice;
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    public function setShipping(?Shipping $shipping): void
    {
        $this->shipping = $shipping;
    }

    public function getDiscount(): ?PriceInterface
    {
        $voucher = $this->getVoucher();
        $subtotalAmount = $this->getSubtotal()?->getAmount();
        if (!$voucher || !$subtotalAmount) {
            return null;
        }

        $discountAMount = match ($voucher->getType()) {
            'percent' => $subtotalAmount * ($voucher->getValue() / 100),
            'amount' => $voucher->getValue() * 100,
            default => throw new RuntimeException('Unsupported voucher type'),
        };

        return Price::fromMoney(Money::ofMinor($discountAMount, 'GBP'));
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        $item->setQuantity($item->getQuantity() + $quantity);
    }

    public function getTotalQuantity(): int
    {
        return array_reduce($this->getItems(), fn(int $carry, BasketItem $item) => $item->getQuantity() + $carry, 0);
    }

    public function getId(): string
    {
        return $this->id ?? throw new DataNotFoundException();
    }

    /**
     * @return list<BasketItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    private function applyVoucher(PriceInterface $price): PriceInterface
    {
        $discount = $this->getDiscount();
        if (!$discount) {
            return $price;
        }

        return $price->getAmount() < $discount->getAmount() ? Price::fromMoney(Money::ofMinor(0, 'GBP')) : $price->minus($discount);
    }

    private function findItemOrCreateNew(Product $product): BasketItem
    {
        if ($item = $this->findItem($product)) {
            return $item;
        }

        $item = new BasketItem(new ResolvedValue($product), 0);
        $this->items[] = $item;

        return $item;
    }

    private function findItem(Product $product): ?BasketItem
    {
        foreach ($this->getItems() as $item) {
            if ($product->getId() === $item->getProduct()->getId()) {
                return $item;
            }
        }

        return null;
    }
}
