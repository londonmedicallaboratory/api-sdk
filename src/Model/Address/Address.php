<?php

declare(strict_types=1);

namespace LML\SDK\Model\Address;

/** @noinspection TypoSafeNamingInspection */

class Address implements AddressInterface
{
    public function __construct(
        private string $id,
        private string $line1,
        private string $postalCode,
        private string $countryCode,
        private ?string $line2 = null,
        private ?string $line3 = null,
    )
    {
    }

    public function toArray()
    {
        return [
            'id'           => $this->getId(),
            'line1'        => $this->getLine1(),
            'line2'        => $this->getLine2(),
            'line3'        => $this->getLine3(),
            'postal_code'  => $this->getPostalCode(),
            'country_name' => $this->getCountryName(),
            'country_code' => $this->getCountryCode(),
        ];
    }

    public function getLine1(): string
    {
        return $this->line1;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function getLine3(): ?string
    {
        return $this->line3;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getAddressLine1(): string
    {
        return $this->line1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->line2;
    }

    public function getAddressLine3(): ?string
    {
        return $this->line3;
    }

    public function getCountryName(): string
    {
        return $this->countryCode;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
