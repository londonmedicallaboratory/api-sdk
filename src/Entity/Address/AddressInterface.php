<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Address;

use Stringable;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      line1: string,
 *      line2?: ?string,
 *      line3?: ?string,
 *      postal_code: string,
 *      country_name?: string,
 *      country_code: string,
 *      city: string,
 *      company?: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface AddressInterface extends ModelInterface, Stringable
{
    public function getAddressLine1(): string;

    public function getAddressLine2(): ?string;

    public function getAddressLine3(): ?string;

    public function getPostalCode(): string;

    public function getCountryName(): string;

    public function getCountryCode(): string;

    public function getCity(): string;

    public function getCompany(): ?string;
}
