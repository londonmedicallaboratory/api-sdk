<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Address\AddressInterface;

/**
 * There is a bug in psalm that prevents `gender` and `ethnicity` to use Enums
 * Due to upcoming enum support, bug will probably not be fixed.
 *
 * @psalm-type S=array{
 *      id: string,
 *      product_id: string,
 *      email: string,
 *      date_of_birth: string,
 *      first_name: string,
 *      last_name: string,
 *      gender: string,
 *      ethnicity: string,
 *      mobile_phone_number: string,
 *      passport_number: string,
 *      nhs_number: ?string,
 *      is_vaccinated: bool,
 *      uk_address?: array{
 *          id: string,
 *          line1: string,
 *          line2: ?string,
 *          line3: ?string,
 *          postal_code: string,
 *          country_name: string,
 *          country_code: string,
 *          city: string,
 *      },
 * }
 *
 * @extends ModelInterface<S>
 */
interface TestRegistrationInterface extends ModelInterface
{
    public function getUkAddress(): ?AddressInterface;

    public function setUkAddress(?AddressInterface $address): void;

    public function getProduct(): ProductInterface;

    public function setProduct(ProductInterface $product): void;

    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getDateOfBirth(): DateTime;

    public function setDateOfBirth(DateTime $dateOfBirth): void;

    public function getFirstName(): string;

    public function setFirstName(string $firstName): void;

    public function getLastName(): string;

    public function setLastName(string $lastName): void;

    /**
     * @return GenderEnum::*
     */
    public function getGender(): string;

    /**
     * @param GenderEnum::* $gender
     */
    public function setGender(string $gender): void;

    /**
     * @return EthnicityEnum::*
     */
    public function getEthnicity(): string;

    /**
     * @param EthnicityEnum::* $ethnicity
     */
    public function setEthnicity(string $ethnicity): void;

    public function getMobilePhoneNumber(): string;

    public function setMobilePhoneNumber(string $mobilePhoneNumber): void;

    public function getPassportNumber(): string;

    public function setPassportNumber(string $passportNumber): void;

    public function getNhsNumber(): ?string;

    public function setNhsNumber(?string $nhsNumber): void;

    public function isVaccinated(): bool;

    public function setIsVaccinated(bool $isVaccinated): void;
}
