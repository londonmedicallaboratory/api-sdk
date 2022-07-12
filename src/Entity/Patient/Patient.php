<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Patient;

use LogicException;
use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Attribute\Entity;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Repository\PatientRepository;
use function sprintf;

#[Entity(repositoryClass: PatientRepository::class, baseUrl: 'patient')]
class Patient implements PatientInterface
{
    public function __construct(
        private string            $firstName,
        private string            $lastName,
        private GenderEnum        $gender,
        private DateTimeInterface $dateOfBirth,
        private ?EthnicityEnum    $ethnicity,
        private ?string           $email,
        private ?string           $foreignId = null,
        private ?string           $id = null,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getId(): string
    {
        return $this->id ?? throw new LogicException('Model has not been saved yet.');
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

    public function getDateOfBirth(): DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTimeInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function setGender(GenderEnum $gender): void
    {
        $this->gender = $gender;
    }

    public function getEthnicity(): ?EthnicityEnum
    {
        return $this->ethnicity;
    }

    public function setEthnicity(?EthnicityEnum $ethnicity): void
    {
        $this->ethnicity = $ethnicity;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->getFirstName(),
            'last_name'     => $this->getLastName(),
            'gender'        => $this->getGender()->value,
            'date_of_birth' => $this->getDateOfBirth()->format('Y-m-d'),
            'ethnicity'     => $this->getEthnicity()?->value,
            'email'         => $this->getEmail(),
            'foreign_id'    => $this->foreignId,
        ];
    }
}
