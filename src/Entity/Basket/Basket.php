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
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Customer\Customer;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Repository\Basket\BasketRepository;
use function array_map;
use function array_reduce;

/**
 * @psalm-import-type S from Address as TAddress
 * @psalm-import-type S from Appointment as TAppointment
 * @psalm-import-type S from Customer as TCustomer
 * @psalm-import-type S from Patient as TPatient
 *
 * @psalm-type S = array{
 *     id: ?string,
 *     affiliate_code?: ?string,
 *     voucher_id: ?string,
 *     shipping_id: ?string,
 *     customer?: ?TCustomer,
 *     patient?: ?TPatient,
 *     items: list<array{product_id: string, quantity: int}>,
 *     shipping_address?: ?TAddress,
 *     billing_address?: ?TAddress,
 *     initial_appointment?: ?TAppointment,
 *     transaction_id?: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: BasketRepository::class, baseUrl: 'basket')]
class Basket implements ModelInterface
{
    /**
     * @param ?LazyValueInterface<?Voucher> $voucher
     * @param ?LazyValueInterface<?Shipping> $shipping
     * @param list<BasketItem> $items
     */
    public function __construct(
        private ?Customer $anonCustomer = null,
        private ?Patient $anonPatient = null,
        private ?Address $shippingAddress = null,
        private ?Address $billingAddress = null,
        private array $items = [],
        private ?LazyValueInterface $shipping = new ResolvedValue(null),
        private ?Appointment $initialAppointment = null,
        private ?string $id = null,
        private ?string $transactionId = null,
        private ?string $affiliateCode = null,
        private ?LazyValueInterface $voucher = null,
    )
    {
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'affiliate_code' => $this->affiliateCode,
            'transaction_id' => $this->transactionId,
            'voucher_id' => $this->getVoucher()?->getId(),
            'shipping_id' => $this->getShipping()?->getId(),
            'items' => array_map(fn(BasketItem $item) => [
                'product_id' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
            ], $this->getItems()),
        ];

        if ($shippingAddress = $this->shippingAddress) {
            $data['shipping_address'] = $shippingAddress->toArray();
        }
        if ($billingAddress = $this->billingAddress) {
            $data['billing_address'] = $billingAddress->toArray();
        }
        $data['initial_appointment'] = $this->initialAppointment?->toArray(); // must be null, or EntityManager won't detect the change
//        if ($initialAppointmentTime = $this->initialAppointment) {
//            $data['initial_appointment'] = $initialAppointmentTime->toArray();
//        }
        if ($customer = $this->getAnonCustomer()) {
            $data['customer'] = [
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phone_number' => $customer->getPhoneNumber(),
            ];
        }
        if ($patient = $this->getAnonPatient()) {
            $data['patient'] = $patient->toArray();
        }

        return $data;
    }

    public function getVoucher(): ?Voucher
    {
        return $this->voucher?->getValue();
    }

    public function setVoucher(?Voucher $voucher): void
    {
        $this->voucher = new ResolvedValue($voucher);
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

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping?->getValue();
    }

    public function setShipping(?Shipping $shipping): void
    {
        $this->shipping = new ResolvedValue($shipping);
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

    public function getAnonCustomer(): ?Customer
    {
        return $this->anonCustomer;
    }

    public function setAnonCustomer(?Customer $anonCustomer): void
    {
        $this->anonCustomer = $anonCustomer;
    }

    public function getAnonPatient(): ?Patient
    {
        return $this->anonPatient;
    }

    public function setAnonPatient(?Patient $patient): void
    {
        $this->anonPatient = $patient;
    }

    /**
     * @return array<int, Shipping>
     */
    public function getAvailableShippingMethods(): array
    {
        $itemsShippingMethods = array_map(fn(BasketItem $basketItem) => $basketItem->getProduct()->getShippingTypes(), $this->getItems());
        foreach ($itemsShippingMethods as $itemShippingMethods) {
            if (!empty($itemShippingMethods)) {
                $itemsShippingMethods = array_intersect(...$itemsShippingMethods);

                return $this->getTotalQuantity() > 1 ? array_filter($itemsShippingMethods, fn(Shipping $shipping) => $shipping->getType() !== 'at_home_phlebotomist') : $itemsShippingMethods;
            }
        }

        return [];
    }

    public function setQuantityForProduct(Product $product, int $quantity): void
    {
        $item = $this->findItemOrCreateNew($product);
        if ($quantity === 0) {
            $this->removeProduct($product);

            return;
        }
        $item->setQuantity($quantity);
    }

    public function getInitialAppointment(): ?Appointment
    {
        return $this->initialAppointment;
    }

    public function setInitialAppointment(?Appointment $initialAppointment): void
    {
        $this->initialAppointment = $initialAppointment;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function getAffiliateCode(): ?string
    {
        return $this->affiliateCode;
    }

    public function setAffiliateCode(?string $affiliateCode): void
    {
        $this->affiliateCode = $affiliateCode;
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

    private function removeProduct(Product $product): void
    {
        foreach ($this->getItems() as $key => $item) {
            if ($product->getId() === $item->getProduct()->getId()) {
                unset($this->items[$key]);
            }
        }
    }
}
