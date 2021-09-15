<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Address\AddressInterface;

class TestRegistration implements TestRegistrationInterface
{
    /**
     * @param LazyValueInterface<ProductInterface> $product
     * @param ?LazyValueInterface<?AddressInterface> $ukAddress
     * @param GenderEnum::* $gender
     * @param EthnicityEnum::* $ethnicity
     */
    public function __construct(
        protected LazyValueInterface  $product,
        protected string              $email,
        protected DateTime            $dateOfBirth,
        protected string              $firstName,
        protected string              $lastName,
        protected string              $gender,
        protected string              $ethnicity,
        protected string              $mobilePhoneNumber,
        protected string              $passportNumber,
        protected ?string             $nhsNumber,
        protected bool                $isVaccinated,
        protected ?LazyValueInterface $ukAddress = null,
        protected string              $id = '',
    )
    {
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

    public function getDateOfBirth(): DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTime $dateOfBirth): void
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

    /**
     * @return GenderEnum::*
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param GenderEnum::* $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return EthnicityEnum::*
     */
    public function getEthnicity(): string
    {
        return $this->ethnicity;
    }

    /**
     * @param EthnicityEnum::* $ethnicity
     */
    public function setEthnicity(string $ethnicity): void
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

    public function getPassportNumber(): string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(string $passportNumber): void
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
        return $this->isVaccinated;
    }

    public function setIsVaccinated(bool $isVaccinated): void
    {
        $this->isVaccinated = $isVaccinated;
    }

    public function getId(): string
    {
        return $this->id;
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
            'is_vaccinated'       => $this->isVaccinated(),
        ];
        if ($ukAddress = $this->getUkAddress()) {
            $data['uk_address'] = $ukAddress->toArray();
        }

        return $data;
    }
}
