<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTime;
use DateTimeInterface;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Enum\VaccinationStatusEnum;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Address\AddressInterface;
use function array_map;
use function array_search;

class TestRegistration implements TestRegistrationInterface
{
    /**
     * @see \LML\SDK\Repository\TestRegistrationRepository::one
     *
     * @param ?LazyValueInterface<?AddressInterface> $selfIsolatingAddress
     * @param list<string> $transitCountries
     * @param LazyValueInterface<list<ProductInterface>> $products
     * @param ?LazyValueInterface<?AddressInterface> $ukAddress
     * @param ?LazyValueInterface<bool> $resultsReady
     */
    public function __construct(
        protected LazyValueInterface     $products,
        protected ?string                $email,
        protected ?DateTimeInterface     $dateOfBirth,
        protected ?string                $firstName,
        protected ?string                $lastName,
        protected ?LazyValueInterface    $resultsReady = null,
        protected ?EthnicityEnum         $ethnicity = null,
        protected ?string                $mobilePhoneNumber = null,
        protected ?string                $passportNumber = null,
        protected ?VaccinationStatusEnum $vaccinationStatus = null,
        protected ?DateTimeInterface     $dateOfArrival = null,
        protected DateTimeInterface      $createdAt = new DateTime(),
        protected ?DateTimeInterface     $completedAt = null,
        protected ?DateTimeInterface     $departureStartDate = null,
        protected ?LazyValueInterface    $ukAddress = null,
        protected ?LazyValueInterface    $selfIsolatingAddress = null,
        protected array                  $transitCountries = [],
        protected string                 $id = '',
    )
    {
    }

    public function getDayOfArrival(): ?DateTimeInterface
    {
        return $this->dateOfArrival;
    }

    public function setDateOfArrival(?DateTimeInterface $date): void
    {
        $this->dateOfArrival = $date;
    }

    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    public function getUkAddress(): ?AddressInterface
    {
        return $this->ukAddress?->getValue();
    }

    public function setUkAddress(?AddressInterface $address): void
    {
        $this->ukAddress = new ResolvedValue($address);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getDateOfBirth(): ?DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTimeInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEthnicity(): ?EthnicityEnum
    {
        return $this->ethnicity;
    }

    public function setEthnicity(?EthnicityEnum $ethnicity): void
    {
        $this->ethnicity = $ethnicity;
    }

    public function getMobilePhoneNumber(): ?string
    {
        return $this->mobilePhoneNumber;
    }

    public function setMobilePhoneNumber(?string $mobilePhoneNumber): void
    {
        $this->mobilePhoneNumber = $mobilePhoneNumber;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): void
    {
        $this->passportNumber = $passportNumber;
    }


    public function isVaccinated(): bool
    {
        return $this->vaccinationStatus === VaccinationStatusEnum::VACCINATED;
    }

    public function getVaccinationStatus(): ?VaccinationStatusEnum
    {
        return $this->vaccinationStatus;
    }

    public function setVaccinationStatus(?VaccinationStatusEnum $vaccinationStatus): void
    {
        $this->vaccinationStatus = $vaccinationStatus;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSelfIsolatingAddress(): ?AddressInterface
    {
        return $this->selfIsolatingAddress?->getValue();
    }

    public function setSelfIsolatingAddress(?AddressInterface $selfIsolatingAddress): void
    {
        $this->selfIsolatingAddress = new ResolvedValue($selfIsolatingAddress);
    }

    /**
     * @return list<string>
     */
    public function getTransitCountryCodes(): array
    {
        return $this->transitCountries;
    }

    public function addTransitCountry(string $code): void
    {
        $this->transitCountries[] = $code;
    }

    public function removeTransitCountry(string $code): void
    {
        $key = array_search($code, $this->transitCountries, true);
        if (false !== $key) {
            unset($this->transitCountries[$key]);
        }
    }

    public function getDepartureStartDate(): ?DateTimeInterface
    {
        return $this->departureStartDate;
    }

    public function setDepartureStartDate(?DateTimeInterface $departureStartDate): void
    {
        $this->departureStartDate = $departureStartDate;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function hasResults(): bool
    {
        return $this->resultsReady?->getValue() ?? false;
    }

    public function toArray(): array
    {
        return [
            'id'                     => $this->getId(),
            'product_ids'            => array_map(fn(ProductInterface $product) => $product->getId(), $this->getProducts()),
            'email'                  => $this->getEmail(),
            'date_of_birth'          => $this->getDateOfBirth()?->format('Y-m-d'),
            'first_name'             => $this->getFirstName(),
            'last_name'              => $this->getLastName(),
            'ethnicity'              => $this->getEthnicity()?->value,
            'mobile_phone_number'    => $this->getMobilePhoneNumber(),
            'passport_number'        => $this->getPassportNumber(),
            'transit_countries'      => $this->transitCountries,
            'departure_start_date'   => $this->getDepartureStartDate()?->format('Y-m-d'),
            'created_at'             => $this->getCreatedAt()->format('Y-m-d'),
            'completed_at'           => $this->getCompletedAt()?->format('Y-m-d'),
            'results_ready'          => $this->hasResults(),
            'self_isolating_address' => $this->getSelfIsolatingAddress()?->toArray(),
            'date_of_arrival'        => $this->getDayOfArrival()?->format('Y-m-d'),
            'uk_address'             => $this->getUkAddress()?->toArray(),
            'vaccination_status'     => $this->getVaccinationStatus()?->value,
        ];
    }
}
