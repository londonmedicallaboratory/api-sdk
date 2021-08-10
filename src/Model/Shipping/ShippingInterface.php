<?php

declare(strict_types=1);

namespace LML\SDK\Model\Shipping;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      type: string,
 *      description: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface ShippingInterface extends ModelInterface
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): ?string;
}
