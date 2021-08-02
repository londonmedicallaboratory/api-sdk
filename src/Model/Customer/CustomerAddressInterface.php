<?php

declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\IdInterface;

interface CustomerAddressInterface extends IdInterface
{
    public function getAddress(): string;

    public function getCountryName(): string;
}
