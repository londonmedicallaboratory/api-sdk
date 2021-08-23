<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\Address\AddressInterface;
use LML\SDK\Model\Customer\CustomerInterface;

class Order implements OrderInterface
{
    public function __construct(
        private string $id,
        private CustomerInterface $customer,
        private AddressInterface $address,
        private ?string $companyName = null,
        private ?AddressInterface $billingAddress = null,
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

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'company' => $this->getCompanyName(),
            'customer' => $this->getCustomer()->toArray(),
            'address' => $this->getAddress()->toArray(),
        ];
    }
}
