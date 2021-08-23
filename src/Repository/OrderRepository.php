<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Order\Order;
use LML\SDK\Model\Order\OrderInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from OrderInterface
 * @extends AbstractViewRepository<S, OrderInterface, array>
 *
 * @see Order
 * @see OrderInterface
 */
class OrderRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $customer = $this->get(CustomerRepository::class)->buildOne($entity['customer']);
        $address = $this->get(AddressRepository::class)->buildOne($entity['address']);

        return new Order(
            id: $entity['id'],
            customer: $customer,
            address: $address,
            companyName: $entity['company'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/order';
    }
}
