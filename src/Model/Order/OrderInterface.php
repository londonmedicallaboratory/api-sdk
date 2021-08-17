<?php

declare(strict_types=1);

namespace LML\SDK\Model\Order;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\Address\AddressInterface;
use LML\SDK\Model\Customer\CustomerInterface;

interface OrderInterface
//    extends ModelInterface
{
    public function getCustomer(): CustomerInterface;

    public function getCompanyName(): ?string;

    public function getAddress(): AddressInterface;

    public function getBillingAddress(): ?AddressInterface;
}
