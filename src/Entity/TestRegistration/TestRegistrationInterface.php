<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTimeInterface;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Enum\VaccinationStatusEnum;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Address\AddressInterface;

/**
 * There is a bug in psalm that prevents `gender` and `ethnicity` to use Enums
 * Due to upcoming enum support, bug will probably not be fixed.
 *
 * @psalm-type S=array{
 *      id: string,
 *      results_ready: bool,
 *      product_ids?: list<string>,
 *      product_skus?: list<string>,
 *      biomarker_ids?: list<string>,
 *      biomarker_codes?: list<string>,
 *      email: ?string,
 *      date_of_birth: ?string,
 *      first_name: ?string,
 *      last_name: ?string,
 *      gender: string,
 *      ethnicity?: ?string,
 *      mobile_phone_number?: ?string,
 *      passport_number?: ?string,
 *      nhs_number?: ?string,
 *      vaccination_status?: ?string,
 *      departure_start_date?: ?string,
 *      transit_countries?: list<string>,
 *      created_at?: ?string,
 *      completed_at?: ?string,
 *      brand_code?: string,
 *      foreign_id?: ?string,
 *      country_from?: string,
 *      transport_type?: string,
 *      date_of_arrival?: ?string,
 *      travel_number?: string,
 *      uk_address?: null|array{
 *          id: string,
 *          line1: string,
 *          line2?: ?string,
 *          line3?: ?string,
 *          postal_code: string,
 *          country_name?: string,
 *          country_code: string,
 *          city: string,
 *      },
 *      self_isolating_address?: null|array{
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

    /**
     * @return list<ProductInterface>
     */
    public function getProducts(): array;

    public function getEmail(): ?string;

    public function getDateOfBirth(): ?DateTimeInterface;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getGender(): GenderEnum;

    public function getGenderName(): string;

    public function setGender(GenderEnum $gender): void;

    public function getEthnicity(): ?EthnicityEnum;

    public function getMobilePhoneNumber(): ?string;

    public function getPassportNumber(): ?string;

    public function getNhsNumber(): ?string;

    public function isVaccinated(): bool;

    public function getVaccinationStatus(): ?VaccinationStatusEnum;

    public function getSelfIsolatingAddress(): ?AddressInterface;

}
