<?php

declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\IdInterface;

interface CustomerInterface extends IdInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;
}
