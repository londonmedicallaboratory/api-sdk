<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use LogicException;
use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\CustomerRepository;
use function sprintf;

/**
 * @see CustomerRepository::one()
 */
#[Entity(repositoryClass: CustomerRepository::class, baseUrl: 'customer')]
class Customer implements CustomerInterface
{
    public function __construct(
        private string  $firstName,
        private string  $lastName,
        private string  $email,
        private ?string $phoneNumber = null,
        private ?string $foreignId = null,
        private ?string $id = null,
        private ?string $password = null,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getId(): string
    {
        return $this->id ?? throw new LogicException('Model has not been saved yet.');
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER', 'ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function toArray()
    {
        $data = [
            'id'           => $this->id,
            'first_name'   => $this->getFirstName(),
            'last_name'    => $this->getLastName(),
            'phone_number' => $this->getPhoneNumber(),
            'email'        => $this->getEmail(),
            'foreign_id'   => $this->foreignId,
        ];
        if (!$this->id && $password = $this->getPassword()) {
            $data['password'] = $password;
        }

        return $data;
    }
}
