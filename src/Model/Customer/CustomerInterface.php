<?php

declare(strict_types=1);

namespace LML\SDK\Model\Customer;

use LML\SDK\Model\IdInterface;

interface CustomerInterface extends IdInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;
}
