<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      first_name: string,
 *      last_name: string,
 *      phone_number: string,
 *      email: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CustomerInterface extends ModelInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getPhoneNumber(): ?string;

    public function getEmail(): string;

}
