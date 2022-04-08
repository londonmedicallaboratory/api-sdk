<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTime;
use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Address\AddressInterface;
use LML\SDK\Entity\Patient\PatientInterface;
use function array_map;

class TestRegistration implements TestRegistrationInterface
{
    /**
     * @see \LML\SDK\Repository\TestRegistrationRepository::one()
     *
     * @param ?LazyValueInterface<?AddressInterface> $selfIsolatingAddress
     * @param list<string> $transitCountries
     * @param LazyValueInterface<list<ProductInterface>> $products
     * @param LazyValueInterface<PatientInterface> $patient
     * @param ?LazyValueInterface<?AddressInterface> $ukAddress
     * @param ?LazyValueInterface<bool> $resultsReady
     */
    public function __construct(
        protected LazyValueInterface  $products,
        protected LazyValueInterface  $patient,
        protected ?LazyValueInterface $resultsReady = null,
        protected ?DateTimeInterface  $dateOfArrival = null,
        protected DateTimeInterface   $createdAt = new DateTime(),
        protected ?DateTimeInterface  $completedAt = null,
        protected ?DateTimeInterface  $departureStartDate = null,
        protected ?LazyValueInterface $ukAddress = null,
        protected ?LazyValueInterface $selfIsolatingAddress = null,
        protected array               $transitCountries = [],
        protected string              $id = '',
    )
    {
    }

    public function getPatient(): PatientInterface
    {
        return $this->patient->getValue();
    }

    public function getDayOfArrival(): ?DateTimeInterface
    {
        return $this->dateOfArrival;
    }

    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    public function getUkAddress(): ?AddressInterface
    {
        return $this->ukAddress?->getValue();
    }

    public function getEmail(): ?string
    {
        return $this->getPatient()->getEmail();
    }

    public function getDateOfBirth(): DateTimeInterface
    {
        return $this->getPatient()->getDateOfBirth();
    }

    public function getFirstName(): string
    {
        return $this->getPatient()->getFirstName();
    }

    public function getLastName(): string
    {
        return $this->getPatient()->getLastName();
    }

    public function getEthnicity(): ?EthnicityEnum
    {
        return $this->getPatient()->getEthnicity();
    }

    public function getGender(): GenderEnum
    {
        return $this->getPatient()->getGender();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSelfIsolatingAddress(): ?AddressInterface
    {
        return $this->selfIsolatingAddress?->getValue();
    }

    /**
     * @return list<string>
     */
    public function getTransitCountryCodes(): array
    {
        return $this->transitCountries;
    }

    public function getDepartureStartDate(): ?DateTimeInterface
    {
        return $this->departureStartDate;
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
            'date_of_birth'          => $this->getDateOfBirth()->format('Y-m-d'),
            'first_name'             => $this->getFirstName(),
            'last_name'              => $this->getLastName(),
            'ethnicity'              => $this->getEthnicity()?->value,
            'transit_countries'      => $this->transitCountries,
            'departure_start_date'   => $this->getDepartureStartDate()?->format('Y-m-d'),
            'created_at'             => $this->getCreatedAt()->format('Y-m-d'),
            'completed_at'           => $this->getCompletedAt()?->format('Y-m-d'),
            'results_ready'          => $this->hasResults(),
            'self_isolating_address' => $this->getSelfIsolatingAddress()?->toArray(),
            'date_of_arrival'        => $this->getDayOfArrival()?->format('Y-m-d'),
            'uk_address'             => $this->getUkAddress()?->toArray(),
            'gender'                 => $this->getGender()->value,
        ];
    }
}
