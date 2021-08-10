<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\Shipping\Shipping;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from ShippingInterface
 * @extends AbstractViewRepository<S, ShippingInterface, array>
 *
 * @see ShippingInterface
 */
class ShippingRepository extends AbstractViewRepository
{
    protected function one($entity, $options, LazyValue $optimizer): ShippingInterface
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
