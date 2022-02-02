<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Attribute\Model;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Model\Address\AddressInterface;
use LML\SDK\Model\Customer\CustomerInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use function array_map;

#[Model(repositoryClass: OrderRepository::class)]
class Order implements OrderInterface
{
    /**
     * @see OrderRepository::one()
     *
     * @param LazyValueInterface<?ShippingInterface> $shipping
     * @param LazyValueInterface<list<ItemInterface>> $items
     */
    public function __construct(
        private string             $id,
        private CustomerInterface  $customer,
        private AddressInterface   $address,
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
        return $this->customer;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAddress(): AddressInterface
    {
        return $this->address;
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
