<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Money\Price;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Exception\DataNotFoundException;

/**
 * @psalm-import-type S from Shipping
 *
 * @extends AbstractRepository<S, Shipping, array>
 */
class ShippingRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Shipping
    {
        $id = $entity['id'];
        $priceData = $entity['price'] ?? throw new DataNotFoundException();

        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        return new Shipping(
            id: $id,
            type: $entity['type'],
            name: $entity['name'],
            description: $entity['description'],
            price: $price,
        );
    }
}
