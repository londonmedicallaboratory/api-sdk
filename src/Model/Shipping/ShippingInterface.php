<?php

declare(strict_types=1);

namespace LML\SDK\Model\Shipping;

use LML\SDK\Model\IdInterface;

interface ShippingInterface extends IdInterface
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): ?string;
}
