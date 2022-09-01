<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use Stringable;
use LML\SDK\Entity\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @psalm-type S=array{
 *      id: ?string,
 *      first_name: string,
 *      last_name: string,
 *      email: string,
 *      phone_number?: ?string,
 *      foreign_id?: ?string,
 *      password?: string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface CustomerInterface extends ModelInterface, Stringable, UserInterface, PasswordAuthenticatedUserInterface
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getEmail(): string;

    public function getPhoneNumber(): ?string;
}
