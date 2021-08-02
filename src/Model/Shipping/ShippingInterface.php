<?php

declare(strict_types=1);

namespace App\Model\Shipping;

use App\Model\IdInterface;

interface ShippingInterface extends IdInterface
{
    public function getType(): string;

    public function getName(): string;

    public function getDescription(): ?string;
}
