<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use LML\SDK\Entity\Address\Address;

interface CustomerInterface
{
    public function getAddress(): ?Address;

    public function getFirstName(): string;

    public function getLastName(): string;

    public function setFirstName(string $firstName): void;

    public function setLastName(string $lastName): void;

    /**
     * @return array{
     *      first_name: string,
     *      last_name: string,
     *      email: string,
     *      phone_number?: ?string,
     *      billing_address_id?: ?string,
     *      is_subscribed_to_newsletter?: bool,
     * }
     */
    public function toArray(): array;
}
