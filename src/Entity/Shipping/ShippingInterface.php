<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Shipping;

use Stringable;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Money\PriceInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      type: string,
 *      description: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @extends ModelInterface<S>
 */
interface ShippingInterface extends ModelInterface, Stringable
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): ?string;

    public function getPrice(): PriceInterface;
}
