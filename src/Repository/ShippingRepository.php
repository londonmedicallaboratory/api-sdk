<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Shipping\ShippingInterface;

/**
 * @psalm-import-type S from ShippingInterface
 * @extends AbstractRepository<S, Shipping, array>
 *
 * @see ShippingInterface
 */
class ShippingRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Shipping
    {
        $id = $entity['id'];

        return new Shipping(
            id         : $id,
            type       : $entity['type'],
            name       : $entity['name'],
            description: $entity['description'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/shipping';
    }
}
