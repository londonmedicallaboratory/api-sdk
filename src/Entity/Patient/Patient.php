<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Patient;

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
        private string            $id,
        private string            $firstName,
        private string            $lastName,
        private GenderEnum        $gender,
        private DateTimeInterface $dateOfBirth,
        private ?EthnicityEnum    $ethnicity,
        private ?string           $email,
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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function getDateOfBirth(): DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function getEthnicity(): ?EthnicityEnum
    {
        return $this->ethnicity;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function toArray()
    {
        return [
            'id'            => $this->getId(),
            'first_name'    => $this->getFirstName(),
            'last_name'     => $this->getLastName(),
            'gender'        => $this->getGender()->value,
            'date_of_birth' => $this->getDateOfBirth()->format('Y-m-d'),
            'ethnicity'     => $this->getEthnicity()?->value,
            'email'         => $this->getEmail(),
        ];
    }
}
