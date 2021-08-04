<?php

declare(strict_types=1);

namespace LML\SDK\Model\Customer;

use LML\SDK\Model\IdInterface;

interface CustomerAddressInterface extends IdInterface
{
    public function getAddress(): string;

    public function getCountryName(): string;
}
