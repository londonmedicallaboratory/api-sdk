<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Enum\OrderStatusEnum;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Address\Address;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Appointment\Appointment;
use function array_map;

/**
 * @template TAppointment of Appointment
 *
 * @psalm-type TItem = array{product_id: string, quantity: int, product_sku?: ?string}
 *
 * @psalm-import-type S from Address as TAddress
 * @psalm-import-type S from Customer as TCustomer
 *
 * @psalm-type S=array{
 *      id: string,
 *      company: ?string,
 *      shipping_id?: ?string,
 *      shipping_date?: ?string,
 *      customer_id?: ?string,
 *      address?: null|TAddress,
 *      address_id?: ?string,
 *      billing_address?: ?TAddress,
 *      customer?: TCustomer,
 *      items: list<TItem>,
 *      price?: array{amount_minor: int, currency: string, formatted_value: string},
 *      status?: ?string,
 *      created_at?: ?string,
 *      order_number?: ?int,
 *      voucher_id?: ?string,
 *      carrier?: ?string,
 *      tracking_number?: ?string,
 *      cancel?: ?bool,
 *      refund?: ?bool,
 *      initial_appointment?: array{
 *          brand_id: string,
 *          appointment_time: string,
 *      },
 * }
 *
 * @implements ModelInterface<S>
 *
 * @see Appointment
 */
#[Entity(repositoryClass: OrderRepository::class, baseUrl: 'order')]
class Order implements ModelInterface
{
    /**
     * @see OrderRepository::one()
     *
     * @param LazyValueInterface<Customer> $customer
     * @param LazyValueInterface<?Address> $address
     * @param LazyValueInterface<?Shipping> $shipping
     * @param LazyValueInterface<list<TAppointment>> $appointments
     * @param LazyValueInterface<?Address> $billingAddress
     * @param LazyValueInterface<list<OrderItem>> $items
     * @param LazyValueInterface<?string> $trackingNumber
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
        protected OrderStatusEnum $status,
        protected ?DateTimeInterface $shippingDate = null,
        protected ?string $companyName = null,
        protected ?DateTimeInterface $createdAt = null,
        protected ?int $orderNumber = null,
        protected ?CarrierEnum $carrier = null,
        protected ?LazyValueInterface $trackingNumber = null,

        // only allowed for POST /api/order; it must **never** be patched, look at issue (NA)
        protected readonly ?Appointment $initialAppointment = null,
    )
    {
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrderStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getStatusName(): string
    {
        return $this->getStatus()->getName();
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping->getValue();
    }

    public function setShipping(?Shipping $shipping): void
    {
        $this->shipping = new ResolvedValue($shipping);
    }

    public function getCustomer(): Customer
    {
        return $this->customer->getValue();
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAddress(): ?Address
    {
        return $this->address->getValue();
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress->getValue();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return list<OrderItem>
     */
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
     * @return list<TAppointment>
     */
    public function getAppointments(): array
    {
        return $this->appointments->getValue();
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber?->getValue();
    }

    public function getCarrier(): ?CarrierEnum
    {
        return $this->carrier;
    }

    public function getInitialAppointment(): ?Appointment
    {
        return $this->initialAppointment;
    }

    public function toArray(): array
    {
        $customerId = $this->getCustomer()->getId();

        $data = [
            'id' => $this->getId(),
            'carrier' => $this->getCarrier()?->value,
            'tracking_number' => $this->getTrackingNumber(),
            'shipping_id' => $this->getShipping()?->getId(),
            'shipping_date' => $this->getShippingDate()?->format('Y-m-d'),
            'company' => $this->getCompanyName(),
            'items' => array_map(static fn(OrderItem $item) => $item->toArray(), $this->getItems()),
        ];
        if ($billingAddress = $this->getBillingAddress()) {
            $data['billing_address'] = $billingAddress->toArray();
        }
        if ($address = $this->getAddress()) {
            $data['address_id'] = $address->getId();
        }
        if ($customerId) {
            $data['customer_id'] = $customerId;
        }
        if ($initialAppointmentTime = $this->initialAppointment) {
            $data['initial_appointment'] = [
                'brand_id' => $initialAppointmentTime->getBrand()->getId(),
                'appointment_time' => $initialAppointmentTime->getAppointmentTime()->format('Y-m-d\TH:i:sP'),
            ];
        }

        return $data;
    }
}
