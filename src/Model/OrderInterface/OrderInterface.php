<?php

declare(strict_types=1);

namespace LML\SDK\Model\OrderInterface;

use LML\SDK\Model\Customer\CustomerInterface;
use LML\SDK\Model\Customer\CustomerAddressInterface;

interface OrderInterface
{
    public function getPhoneNumber(): ?string;

    public function getCompanyName(): ?string;

    public function getCustomer(): CustomerInterface;

    public function getAddress(): CustomerAddressInterface;

    public function getEmail(): string;
}
