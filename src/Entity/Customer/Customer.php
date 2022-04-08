<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use LML\SDK\Repository\CustomerRepository;
use function sprintf;

/**
 * @see CustomerRepository::one()
 */
class Customer implements CustomerInterface
{
    public function __construct(
        private string  $id,
        private string  $firstName,
        private string  $lastName,
        private string  $email,
        private ?string $phoneNumber,
        private ?string $foreignId = null,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function toArray()
    {
        return [
            'id'           => $this->getId(),
            'first_name'   => $this->getFirstName(),
            'last_name'    => $this->getLastName(),
            'phone_number' => $this->getPhoneNumber(),
            'email'        => $this->getEmail(),
            'foreign_id'   => $this->foreignId,
        ];
    }
}
