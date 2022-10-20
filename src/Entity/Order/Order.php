<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Enum\OrderStatusEnum;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Entity\Customer\CustomerInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;
use function array_map;

/**
 * @template TAppointments of Appointment
 *
 * @see Appointment
 */
#[Entity(repositoryClass: OrderRepository::class, baseUrl: 'order')]
class Order implements OrderInterface
{
    /**
     * @see OrderRepository::one()
     *
     * @param LazyValueInterface<CustomerInterface> $customer
     * @param LazyValueInterface<AddressInterface> $address
     * @param LazyValueInterface<?ShippingInterface> $shipping
     * @param LazyValueInterface<list<TAppointments>> $appointments
     * @param LazyValueInterface<?AddressInterface> $billingAddress
     * @param LazyValueInterface<list<ItemInterface>> $items
     */
    public function __construct(
        protected string $id,
        protected LazyValueInterface $customer,
        protected LazyValueInterface $address,
        protected PriceInterface $total,
        protected LazyValueInterface $items,
        protected LazyValueInterface $shipping,
        protected LazyValueInterface $appointments,
        protected LazyValueInterface $billingAddress,
        protected ?DateTimeInterface $shippingDate = null,
        protected ?string $companyName = null,
        protected ?OrderStatusEnum $status = null,
        protected ?DateTimeInterface $createdAt = null,
        protected ?int $orderNumber = null,
    )
    {
    }

    public function getStatus(): ?OrderStatusEnum
    {
        return $this->status;
    }

    public function getStatusName(): ?string
    {
        return $this->getStatus()?->getName();
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function getShipping(): ?ShippingInterface
    {
        return $this->shipping->getValue();
    }

    public function setShipping(?Shipping $shipping): void
    {
        $this->shipping = new ResolvedValue($shipping);
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
        return $this->billingAddress->getValue();
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

    public function getShippingDate(): ?DateTimeInterface
    {
        return $this->shippingDate;
    }

    /**
     * @return list<TAppointments>
     */
    public function getAppointments(): array
    {
        return $this->appointments->getValue();
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'customer_id' => $this->getCustomer()->getId(),
            'shipping_id' => $this->getShipping()?->getId(),
            'shipping_date' => $this->getShippingDate()?->format('Y-m-d'),
            'company' => $this->getCompanyName(),
            'customer' => $this->getCustomer()->toArray(),
            'address' => $this->getAddress()->toArray(),
            'price' => $this->getTotal()->toArray(),
            'items' => array_map(static fn(ItemInterface $item) => $item->toArray(), $this->getItems()),
        ];
        if ($billingAddress = $this->getBillingAddress()) {
            $data['billing_address'] = $billingAddress->toArray();
        }

        return $data;
    }
}
