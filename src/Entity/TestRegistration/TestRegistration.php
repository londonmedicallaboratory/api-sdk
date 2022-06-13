<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTime;
use DateTimeInterface;
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
     * @param LazyValueInterface<?PatientInterface> $patient
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
        protected ?DateTimeInterface  $patientRegisteredAt = null,
        protected ?DateTimeInterface  $departureStartDate = null,
        protected ?LazyValueInterface $ukAddress = null,
        protected ?LazyValueInterface $selfIsolatingAddress = null,
        protected array               $transitCountries = [],
        protected ?string             $doctorsNote = null,
        protected string              $id = '',
    )
    {
    }

    public function getPatient(): ?PatientInterface
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

    public function getPatientRegisteredAt(): ?DateTimeInterface
    {
        return $this->patientRegisteredAt;
    }

    public function getDoctorsNote(): ?string
    {
        return $this->doctorsNote;
    }

    public function hasResults(): bool
    {
        return $this->resultsReady?->getValue() ?? false;
    }

    public function toArray(): array
    {
        $patient = $this->getPatient();
        $productIds = array_map(static fn(ProductInterface $product) => $product->getId(), $this->getProducts());

        return [
            'id'                     => $this->getId(),
            'patient_id'             => $patient?->getId(),
            'results_ready'          => $this->hasResults(),
            'product_ids'            => $productIds,
            'email'                  => $patient?->getEmail(),
            'date_of_birth'          => $patient?->getDateOfBirth()->format('Y-m-d'),
            'first_name'             => $patient?->getFirstName(),
            'last_name'              => $patient?->getLastName(),
            'ethnicity'              => $patient?->getEthnicity()?->value,
            'gender'                 => $patient?->getGender()->value,
            'transit_countries'      => $this->getTransitCountryCodes(),
            'departure_start_date'   => $this->getDepartureStartDate()?->format('Y-m-d'),
            'created_at'             => $this->getCreatedAt()->format('Y-m-d'),
            'completed_at'           => $this->getCompletedAt()?->format('Y-m-d'),
            'patient_registered_at'  => $this->getPatientRegisteredAt()?->format('Y-m-d'),
            'self_isolating_address' => $this->getSelfIsolatingAddress()?->toArray(),
            'date_of_arrival'        => $this->getDayOfArrival()?->format('Y-m-d'),
            'uk_address'             => $this->getUkAddress()?->toArray(),
            'doctors_note'           => $this->getDoctorsNote(),
        ];
    }
}
