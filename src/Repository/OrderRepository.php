<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\Order\Order;
use LML\SDK\Model\Money\Price;
use LML\SDK\Model\Order\BasketItem;
use LML\SDK\Model\Order\OrderInterface;
use LML\SDK\Service\Model\AbstractRepository;

/**
 * @psalm-import-type S from OrderInterface
 * @extends AbstractRepository<S, OrderInterface, array>
 *
 * @see Order
 * @see OrderInterface
 */
class OrderRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $customer = $this->get(CustomerRepository::class)->buildOne($entity['customer']);
        $address = $this->get(AddressRepository::class)->buildOne($entity['address']);

        $priceData = $entity['price'];
        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        return new Order(
            id: $entity['id'],
            customer: $customer,
            address: $address,
            total: $price,
            companyName: $entity['company'],
            items: new LazyValue(fn() => $this->createItems($entity['items'])),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/order';
    }

    /**
     * @param list<array{product_id: string, quantity: int}> $items
     *
     * @return list<BasketItem>
     */
    private function createItems(array $items)
    {
        $list = [];
        foreach ($items as ['product_id' => $productId, 'quantity' => $quantity]) {
            $product = $this->get(ProductRepository::class)->findOrThrowException(id: $productId, await: true);
            $list[] = new BasketItem($product, $quantity);
        }

        return $list;
    }
}
