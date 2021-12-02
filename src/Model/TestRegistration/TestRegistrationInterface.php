<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestRegistration;

use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Model\ModelInterface;
use LML\SDK\Enum\VaccinationStatusEnum;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Address\AddressInterface;

/**
 * There is a bug in psalm that prevents `gender` and `ethnicity` to use Enums
 * Due to upcoming enum support, bug will probably not be fixed.
 *
 * @psalm-type S=array{
 *      id: string,
 *      results_ready: bool,
 *      product_id: string,
 *      email: string,
 *      date_of_birth: string,
 *      first_name: string,
 *      last_name: string,
 *      gender: string,
 *      ethnicity?: ?string,
 *      mobile_phone_number: string,
 *      passport_number?: ?string,
 *      nhs_number?: ?string,
 *      vaccination_status?: ?string,
 *      non_exempt_date: ?string,
 *      transit_countries?: list<string>,
 *      created_at?: ?string,
 *      completed_at?: ?string,
 *      client_code?: string,
 *      country_from?: string,
 *      transport_type?: string,
 *      date_of_arrival?: string,
 *      travel_number?: string,
 *      uk_address?: array{
 *          id: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string,
 *          country_name?: string,
 *          country_code: string,
 *          city: string,
 *      },
 *      self_isolating_address?: array{
 *          id: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string,
 *          country_name?: string,
 *          country_code: string,
 *          city: string,
 *      },
 * }
 *
 * @extends ModelInterface<S>
 */
interface TestRegistrationInterface extends ModelInterface
{
    public function hasResults(): bool;

    public function getCreatedAt(): DateTimeInterface;

    public function getCompletedAt(): ?DateTimeInterface;

    /**
     * @return list<string>
     */
    public function getTransitCountryCodes(): array;

    public function getUkAddress(): ?AddressInterface;

    public function getProduct(): ProductInterface;

    public function getEmail(): string;

    public function getDateOfBirth(): DateTimeInterface;

    public function getFirstName(): string;

    public function getLastName(): string;

    /**
     * @return GenderEnum::*
     */
    public function getGender(): string;

    public function getGenderName(): string;

    /**
     * @param GenderEnum::* $gender
     */
    public function setGender(string $gender): void;

    /**
     * @return null|EthnicityEnum::*
     */
    public function getEthnicity(): ?string;

    public function getMobilePhoneNumber(): string;

    public function getPassportNumber(): ?string;

    public function getNhsNumber(): ?string;

    public function isVaccinated(): bool;

    /**
     * @return ?VaccinationStatusEnum::*
     */
    public function getVaccinationStatus(): ?string;
}
