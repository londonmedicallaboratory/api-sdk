<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use RuntimeException;
use LML\SDK\DTO\Payment;
use LML\SDK\Service\Basket;
use LML\View\Lazy\LazyValue;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Enum\OrderStatusEnum;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Order\BasketItem;
use LML\SDK\Entity\Shipping\Shipping;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Order\OrderInterface;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use function sprintf;
use function React\Promise\resolve;

/**
 * @psalm-import-type S from OrderInterface
 *
 * @extends AbstractRepository<S, Order, array>
 */
class OrderRepository extends AbstractRepository
{
    public function create(Payment $payment, Basket $basket): Order
    {
        $customerRepository = $this->get(CustomerRepository::class);

        $customer = $customerRepository->createFromPayment($payment);
        $customerRepository->persist($customer);
        $customerRepository->flush();

        $deliveryAddress = $payment->deliveryAddress ?? $payment->billingAddress;

        $order = new Order(
            id: '',
            customer: new ResolvedValue($customer),
            address: new ResolvedValue($deliveryAddress ?? throw new RuntimeException()),
            total: $basket->getTotal() ?? throw new RuntimeException(),
            items: new LazyValue(fn() => $basket->getItems()),
            companyName: $payment->customersCompany,
            billingAddress: new ResolvedValue($payment->deliveryAddress ? null : $payment->billingAddress),
            shipping: new ResolvedValue($payment->shipping),
            appointments: new LazyValue(fn() => []),
        );

        $this->persist($order);
        $this->flush();

        return $order;
    }

    protected function one($entity, $options, $optimizer): Order
    {
        $customer = $this->get(CustomerRepository::class)->buildOne($entity['customer']);
        $address = $this->get(AddressRepository::class)->buildOne($entity['address']);

        $priceData = $entity['price'];
        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        $id = $entity['id'];

        $shippingDate = $entity['shipping_date'] ?? null;
        $createdAt = $entity['created_at'] ?? null;
        $status = $entity['status'] ?? null;

        return new Order(
            id: $id,
            customer: new ResolvedValue($customer),
            shippingDate: $shippingDate ? new DateTime($shippingDate) : null,
            address: new ResolvedValue($address),
            billingAddress: new ResolvedValue(null),
            total: $price,
            companyName: $entity['company'],
            items: new ResolvedValue($this->createItems($entity['items'])),
            shipping: $this->getShipping($entity),
            appointments: new LazyPromise($this->getAppointments($id)),
            status: $status ? OrderStatusEnum::tryFrom($status) : null,
            createdAt: $createdAt ? new DateTime($createdAt) : null,
            orderNumber: $entity['order_number'] ?? null,
        );
    }

    /**
     * @return PromiseInterface<list<Appointment>>
     */
    private function getAppointments(string $_id): PromiseInterface
    {
        return resolve([]);
//        $url = sprintf('/order/%s/appointments', $id);
//
//        return $this->get(ShippingRepository::class)->findOneByUrl(url: $url);
    }

    /**
     * @param S $entity
     *
     * @return LazyValueInterface<?Shipping>
     */
    private function getShipping(array $entity): LazyValueInterface
    {
        if (!$id = $entity['id']) {
            return new ResolvedValue(null);
        }
        $shippingId = $entity['shipping_id'] ?? null;
        if (!$shippingId) {
            return new ResolvedValue(null);
        }

        $url = sprintf('/order/%s/shipping', $id);
        $promise = $this->get(ShippingRepository::class)->findOneBy(url: $url);

        return new LazyPromise($promise);
    }

    /**
     * @param list<array{product_id: string, quantity: int}> $items
     *
     * @return list<BasketItem>
     */
    private function createItems(array $items): array
    {
        return array_map(function (array $item) {
            $productPromise = $this->get(ProductRepository::class)->fetch(id: $item['product_id']);

            return new BasketItem(new LazyPromise($productPromise), $item['quantity']);
        }, $items);
    }
}
