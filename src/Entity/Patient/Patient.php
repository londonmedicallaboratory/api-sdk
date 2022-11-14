<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Patient;

use Stringable;
use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Attribute\Entity;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Address\Address;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\PatientRepository;
use LML\SDK\Exception\EntityNotPersistedException;
use function sprintf;

/**
 * @psalm-type S=array{
 *      id: ?string,
 *      first_name: string,
 *      last_name: string,
 *      gender: string,
 *      date_of_birth: string,
 *      phone_number?: ?string,
 *      ethnicity?: ?string,
 *      foreign_id?: ?string,
 *      email?: ?string,
 *      address_id?: ?string,
 * }
 *
 * @experimental ModelInterface<S>
 */
#[Entity(repositoryClass: PatientRepository::class, baseUrl: 'patient')]
class Patient implements ModelInterface, Stringable
{
    /**
     * @param LazyValueInterface<?Address> $address
     */
    public function __construct(
        private string $firstName,
        private string $lastName,
        private GenderEnum $gender,
        private DateTimeInterface $dateOfBirth,
        private ?EthnicityEnum $ethnicity,
        private LazyValueInterface $address,
        private ?string $email,
        private ?string $foreignId = null,
        private ?string $phoneNumber = null,
        private ?string $id = null,
    )
    {
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getId(): string
    {
        return $this->id ?? throw new EntityNotPersistedException();
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getAddress(): ?Address
    {
        return $this->address->getValue();
    }

    public function setAddress(?Address $address): void
    {
        $this->address = new ResolvedValue($address);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'gender' => $this->getGender()->value,
            'date_of_birth' => $this->getDateOfBirth()->format('Y-m-d'),
            'ethnicity' => $this->getEthnicity()?->value,
            'email' => $this->getEmail(),
            'foreign_id' => $this->foreignId,
            'phone_number' => $this->getPhoneNumber(),
            'address_id' => $this->getAddress()?->getId(),
        ];
    }
}
