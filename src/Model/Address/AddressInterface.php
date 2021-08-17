<?php

declare(strict_types=1);

namespace LML\SDK\Model\Address;

use LML\SDK\Model\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      line1: string,
 *      line2: ?string,
 *      line3: ?string,
 *      postal_code: string,
 *      country_name: ?string,
 *      country_code: string,
 * }
 *
 * @extends ModelInterface<S>
 *
 * @noinspection TypoSafeNamingInspection
 */
interface AddressInterface extends ModelInterface
{
    public function getAddressLine1(): string;

    public function getAddressLine2(): ?string;

    public function getAddressLine3(): ?string;

    public function getCountryName(): string;

    public function getPostalCode(): string;

    public function getCountryCode(): string;
}
