<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\Shipping\ShippingInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from ShippingInterface
 * @extends AbstractRepository<S, ShippingInterface, array>
 *
 * @see ShippingInterface
 */
class ShippingRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): ShippingInterface
    {
        $id = $entity['id'];

        return new Shipping(
            id: $id,
            type: $entity['type'],
            name: $entity['name'],
            description: $entity['description'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/shipping';
    }
}
