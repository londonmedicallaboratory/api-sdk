<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Enum\VaccinationStatusEnum;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Address\AddressInterface;
use function array_search;

class TestRegistration implements TestRegistrationInterface
{
    /**
     * @param LazyValueInterface<ProductInterface> $product
     * @param ?LazyValueInterface<?AddressInterface> $ukAddress
     * @param ?LazyValueInterface<?AddressInterface> $selfIsolatingAddress
     * @param GenderEnum::* $gender
     * @param null|EthnicityEnum::* $ethnicity
     * @param ?VaccinationStatusEnum::* $vaccinationStatus
     * @param list<string> $transitCountries
     */
    public function __construct(
        protected LazyValueInterface  $product,
        protected string              $email,
        protected DateTimeInterface   $dateOfBirth,
        protected string              $firstName,
        protected string              $lastName,
        protected string              $gender,
        protected ?string             $ethnicity,
        protected string              $mobilePhoneNumber,
        protected ?string             $passportNumber,
        protected ?string             $nhsNumber,
        protected ?string             $vaccinationStatus,
        protected DateTimeInterface   $dateOfArrival,
        protected DateTimeInterface   $createdAt,
        protected bool                $resultsReady = false,
        protected ?DateTimeInterface  $completedAt = null,
        protected ?DateTimeInterface  $nonExemptDay = null,
        protected ?LazyValueInterface $ukAddress = null,
        protected ?LazyValueInterface $selfIsolatingAddress = null,
        protected array               $transitCountries = [],
        protected string              $id = '',
    )
    {
    }

    public function getDayOfArrival(): DateTimeInterface
    {
        return $this->dateOfArrival;
    }

    public function setDateOfArrival(DateTimeInterface $date): void
    {
        $this->dateOfArrival = $date;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product->getValue();
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = new ResolvedValue($product);
    }

    public function getUkAddress(): ?AddressInterface
    {
        return $this->ukAddress?->getValue();
    }

    public function setUkAddress(?AddressInterface $address): void
    {
        $this->ukAddress = new ResolvedValue($address);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getDateOfBirth(): DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTimeInterface $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
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

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getGenderName(): string
    {
        $gender = $this->getGender();

        return GenderEnum::getViewFormat($gender);
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getEthnicity(): ?string
    {
        return $this->ethnicity;
    }

    /**
     * @param null|EthnicityEnum::* $ethnicity
     */
    public function setEthnicity(?string $ethnicity): void
    {
        $this->ethnicity = $ethnicity;
    }

    public function getMobilePhoneNumber(): string
    {
        return $this->mobilePhoneNumber;
    }

    public function setMobilePhoneNumber(string $mobilePhoneNumber): void
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

    public function getNhsNumber(): ?string
    {
        return $this->nhsNumber;
    }

    public function setNhsNumber(?string $nhsNumber): void
    {
        $this->nhsNumber = $nhsNumber;
    }

    public function isVaccinated(): bool
    {
        return $this->vaccinationStatus === VaccinationStatusEnum::VACCINATED;
    }

    /**
     * @return ?VaccinationStatusEnum::*
     */
    public function getVaccinationStatus(): ?string
    {
        return $this->vaccinationStatus;
    }

    /**
     * @param ?VaccinationStatusEnum::* $vaccinationStatus
     */
    public function setVaccinationStatus(?string $vaccinationStatus): void
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

    public function toArray()
    {
        $data = [
            'id'                  => $this->getId(),
            'product_id'          => $this->getProduct()->getId(),
            'email'               => $this->getEmail(),
            'date_of_birth'       => $this->getDateOfBirth()->format('Y-m-d'),
            'first_name'          => $this->getFirstName(),
            'last_name'           => $this->getLastName(),
            'gender'              => $this->getGender(),
            'ethnicity'           => $this->getEthnicity(),
            'mobile_phone_number' => $this->getMobilePhoneNumber(),
            'passport_number'     => $this->getPassportNumber(),
            'nhs_number'          => $this->getNhsNumber(),
            'vaccination_status'  => $this->getVaccinationStatus(),
            'transit_countries'   => $this->transitCountries,
            'non_exempt_date'     => $this->getNonExemptDay()?->format('Y-m-d'),
            'created_at'          => $this->getCreatedAt()->format('Y-m-d'),
            'completed_at'        => $this->getCompletedAt()?->format('Y-m-d'),
            'results_ready'       => $this->resultsReady,
        ];
        if ($ukAddress = $this->getUkAddress()) {
            $data['uk_address'] = $ukAddress->toArray();
        }
        if ($selfIsolatingAddress = $this->getSelfIsolatingAddress()) {
            $data['self_isolating_address'] = $selfIsolatingAddress->toArray();
        }

        return $data;
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

    public function getNonExemptDay(): ?DateTimeInterface
    {
        return $this->nonExemptDay;
    }

    public function setNonExemptDay(?DateTimeInterface $nonExemptDay): void
    {
        $this->nonExemptDay = $nonExemptDay;
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
        return $this->resultsReady;
    }
}
