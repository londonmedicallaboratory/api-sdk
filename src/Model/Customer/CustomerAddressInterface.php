<?php

declare(strict_types=1);

namespace LML\SDK\Model\Customer;

use LML\SDK\Model\ModelInterface;

interface CustomerAddressInterface extends ModelInterface
{
    public function getAddress(): string;

    public function getCountryName(): string;
}
