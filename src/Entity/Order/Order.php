<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Entity\Customer\CustomerInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;
use function array_map;

#[Entity(repositoryClass: OrderRepository::class)]
class Order implements OrderInterface
{
    /**
     * @see OrderRepository::one()
     *
     * @param LazyValueInterface<CustomerInterface> $customer
     * @param LazyValueInterface<AddressInterface> $address
     * @param LazyValueInterface<?ShippingInterface> $shipping
     * @param LazyValueInterface<list<ItemInterface>> $items
     */
    public function __construct(
        private string             $id,
        private LazyValueInterface $customer,
        private LazyValueInterface $address,
        private PriceInterface     $total,
        private LazyValueInterface $items,
        private LazyValueInterface $shipping,
        private ?string            $companyName = null,
        private ?AddressInterface  $billingAddress = null,
    )
    {
    }

    public function getShipping(): ?ShippingInterface
    {
        return $this->shipping->getValue();
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer->getValue();
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAddress(): AddressInterface
    {
        return $this->address->getValue();
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items->getValue();
    }

    public function getTotal(): PriceInterface
    {
        return $this->total;
    }

    public function toArray(): array
    {
        $price = $this->getTotal();

        return [
            'id'          => $this->getId(),
            'shipping_id' => $this->getShipping()?->getId(),
            'company'     => $this->getCompanyName(),
            'customer'    => $this->getCustomer()->toArray(),
            'address'     => $this->getAddress()->toArray(),
            'price'       => [
                'amount_minor'    => $price->getAmount(),
                'currency'        => $price->getCurrency(),
                'formatted_value' => $price->getFormattedValue(),
            ],
            'items'       => array_map(fn(ItemInterface $item) => $item->toArray(), $this->getItems()),
        ];
    }
}
