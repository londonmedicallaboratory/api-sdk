<?php

declare(strict_types=1);

namespace LML\SDK\Model\Customer;

use LML\SDK\Model\ModelInterface;

/**
 * @noinspection TypoSafeNamingInspection
 */
interface CustomerAddressInterface extends ModelInterface
{
    public function getAddressLine1(): string;

    public function getAddressLine2(): ?string;

    public function getAddressLine3(): ?string;

    public function getCountryName(): string;

    public function getPostalCode(): string;

    public function getCountryCode(): string;
}
