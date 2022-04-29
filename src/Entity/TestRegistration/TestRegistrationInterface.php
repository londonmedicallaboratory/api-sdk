<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestRegistration;

use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Address\AddressInterface;

/**
 * There is a bug in psalm that prevents `gender` and `ethnicity` to use Enums
 * Due to upcoming enum support, bug will probably not be fixed.
 *
 * @psalm-type S=array{
 *      id: string,
 *      patient_id?: ?string,
 *      results_ready: bool,
 *      product_ids?: list<string>,
 *      product_skus?: list<string>,
 *      biomarker_ids?: list<string>,
 *      biomarker_codes?: list<string>,
 *      email?: ?string,
 *      date_of_birth?: string,
 *      first_name: ?string,
 *      last_name: ?string,
 *      ethnicity?: ?string,
 *      gender?: ?string,
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
 *      doctors_note?: ?string,
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

    public function getDoctorsNote(): ?string;

    /**
     * @return list<string>
     */
    public function getTransitCountryCodes(): array;

    public function getUkAddress(): ?AddressInterface;

    /**
     * @return list<ProductInterface>
     */
    public function getProducts(): array;
}
