<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\View\Lazy\LazyValue;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Money\Price;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Order\BasketItem;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Order\OrderInterface;
use LML\SDK\Service\API\AbstractRepository;
use function sprintf;

/**
 * @psalm-import-type S from OrderInterface
 * @extends AbstractRepository<S, Order, array>
 */
class OrderRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Order
    {
        $customer = $this->get(CustomerRepository::class)->buildOne($entity['customer']);
        $address = $this->get(AddressRepository::class)->buildOne($entity['address']);

        $priceData = $entity['price'];
        $price = new Price(
            amount        : $priceData['amount_minor'],
            currency      : $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        $id = $entity['id'];

        $shippingDate = $entity['shipping_date'] ?? null;

        return new Order(
            id          : $id,
            customer    : new ResolvedValue($customer),
            shippingDate: $shippingDate ? new DateTime($shippingDate) : null,
            address     : new ResolvedValue($address),
            total       : $price,
            companyName : $entity['company'],
            items       : new LazyValue(fn() => $this->createItems($entity['items'])),
            shipping    : new LazyPromise($this->getShipping($id)),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/order';
    }

    /**
     * @return PromiseInterface<?Shipping>
     */
    private function getShipping(string $id): PromiseInterface
    {
        $url = sprintf('/order/%s/shipping', $id);

        return $this->get(ShippingRepository::class)->findOneByUrl(url: $url);
    }

    /**
     * @param list<array{product_id: string, quantity: int}> $items
     *
     * @return list<BasketItem>
     */
    private function createItems(array $items): array
    {
        $list = [];
        foreach ($items as ['product_id' => $productId, 'quantity' => $quantity]) {
            $product = $this->get(ProductRepository::class)->findOrThrowException(id: $productId, await: true);
            $list[] = new BasketItem($product, $quantity);
        }

        return $list;
    }
}
