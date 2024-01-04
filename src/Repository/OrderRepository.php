<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LogicException;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Entity\Order\OrderItem;
use LML\SDK\Entity\Shipping\Shipping;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Order\CarrierEnum;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Entity\Order\OrderPaymentStatusEnum;
use LML\SDK\Entity\Order\OrderShippingStatusEnum;
use function sprintf;
use function React\Promise\resolve;
use function Clue\React\Block\await;

/**
 * @psalm-import-type S from Order
 *
 * @extends AbstractRepository<S, Order, array>
 */
class OrderRepository extends AbstractRepository
{
    public function getPersistenceGraph(ModelInterface $view): iterable
    {
        yield $view->getCustomer();
        yield $view->getAddress();
        yield $view->getBillingAddress();
    }

    /**
     * @deprecated
     */
    public function setStatusAsPaid(Order $order): void
    {
        await($this->getClient()->patch('/order', $order->getId(), ['status' => OrderPaymentStatusEnum::PAID->value]));
        $order->setPaymentStatus(OrderPaymentStatusEnum::PAID);
    }

    protected function one($entity, $options, $optimizer): Order
    {
        $id = $entity['id'];

        $addressId = $entity['address_id'] ?? null;
        $address = $addressId ? new LazyPromise($this->getAddress($id)) : new ResolvedValue(null);
        $priceData = $entity['price'] ?? throw new DataNotFoundException();
        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        $shippingDate = $entity['shipping_date'] ?? null;
        $createdAt = $entity['created_at'] ?? null;
        $paymentStatus = $entity['payment_status'] ?? '';
        $shippingStatus = $entity['shipping_status'] ?? '';

        $carrier = $entity['carrier'] ?? null;

        return new Order(
            id: $id,
            customer: new LazyPromise($this->getCustomer($entity)),
            shippingDate: $shippingDate ? new DateTime($shippingDate) : null,
            address: $address,
            billingAddress: new ResolvedValue(null),
            total: $price,
            companyName: $entity['company'],
            items: new ResolvedValue($this->createItems($entity['items'])),
            shipping: $this->getShipping($entity),
            appointments: new LazyPromise($this->getAppointments($id)),
            paymentStatus: OrderPaymentStatusEnum::tryFrom($paymentStatus) ?? OrderPaymentStatusEnum::PAID,
            shippingStatus: OrderShippingStatusEnum::tryFrom($shippingStatus) ?? OrderShippingStatusEnum::AWAITING_SHIPPING,
            createdAt: $createdAt ? new DateTime($createdAt) : null,
            orderNumber: $entity['order_number'] ?? null,
            carrier: $carrier ? CarrierEnum::from($carrier) : null,
            trackingNumber: new ResolvedValue($entity['tracking_number'] ?? null),
        );
    }

    /**
     * @return PromiseInterface<?Address>
     */
    private function getAddress(string $id): PromiseInterface
    {
        $url = sprintf('/order/%s/address', $id);

        return $this->get(AddressRepository::class)->findOneBy(url: $url);
    }

    /**
     * @param S $entity
     *
     * @return PromiseInterface<Customer>
     */
    private function getCustomer($entity): PromiseInterface
    {
        $customerRepository = $this->get(CustomerRepository::class);
        if ($customerId = $entity['customer_id'] ?? null) {
            return $customerRepository->fetch($customerId);
        }
        $struct = $entity['customer'] ?? throw new LogicException();

        return resolve($customerRepository->buildOne($struct));
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
     * @param list<array{product_id: string, quantity: int, product_sku?: ?string}> $items
     *
     * @return list<OrderItem>
     */
    private function createItems(array $items): array
    {
        return array_map(function (array $item) {
            $productPromise = $this->get(ProductRepository::class)->fetch(id: $item['product_id']);

            return new OrderItem(new LazyPromise($productPromise), $item['quantity']);
        }, $items);
    }
}
