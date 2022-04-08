<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Customer;

use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Repository\CustomerRepository;
use function sprintf;

/**
 * @see CustomerRepository::one()
 */
class Customer implements CustomerInterface
{
    public function __construct(
        private string             $id,
        private string             $firstName,
        private string             $lastName,
        private string             $email,
        private string             $phoneNumber,
        private ?string            $nhsNumber,
        private ?DateTimeInterface $dateOfBirth,
        private ?GenderEnum        $gender,
        private ?string            $foreignId = null,
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

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getDateOfBirth(): ?DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function getNhsNumber(): ?string
    {
        return $this->nhsNumber;
    }

    public function getGender(): ?GenderEnum
    {
        return $this->gender;
    }

    public function getGenderName(): ?string
    {
        return $this->getGender()?->getName();
    }

    public function toArray()
    {
        return [
            'id'            => $this->getId(),
            'first_name'    => $this->getFirstName(),
            'last_name'     => $this->getLastName(),
            'phone_number'  => $this->getPhoneNumber(),
            'email'         => $this->getEmail(),
            'date_of_birth' => $this->getDateOfBirth()?->format('Y-m-d'),
            'nhs_number'    => $this->getNhsNumber(),
            'gender'        => $this->getGender()?->value,
            'foreign_id'    => $this->foreignId,
        ];
    }
}
