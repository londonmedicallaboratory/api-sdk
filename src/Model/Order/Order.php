<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\Address\AddressInterface;
use LML\SDK\Model\Customer\CustomerInterface;

class Order implements OrderInterface
{
    public function __construct(
        private CustomerInterface $customer,
        private ?string $companyName,
        private AddressInterface $address,
        private ?AddressInterface $billingAddress,
    )
    {
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }
}
