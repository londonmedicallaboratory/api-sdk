<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use Stringable;
use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      first_name: string,
 *      last_name: string,
 *      phone_number: string,
 *      email: string,
 *      date_of_birth: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CustomerInterface extends ModelInterface, Stringable
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getPhoneNumber(): ?string;

    public function getEmail(): string;

    public function getDateOfBirth(): DateTimeInterface;
}
