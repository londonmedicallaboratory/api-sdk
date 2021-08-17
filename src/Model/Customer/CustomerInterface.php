<?php

declare(strict_types=1);

namespace LML\SDK\Model\Customer;

use LML\SDK\Model\ModelInterface;

interface CustomerInterface extends ModelInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getPhoneNumber(): ?string;

    public function getEmail(): string;

}
