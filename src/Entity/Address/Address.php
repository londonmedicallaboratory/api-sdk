<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Address;

use LML\SDK\Attribute\Entity;
use LML\SDK\Repository\AddressRepository;

/** @noinspection TypoSafeNamingInspection */

#[Entity(repositoryClass: AddressRepository::class)]
class Address implements AddressInterface
{
    public function __construct(
        private string  $id,
        private string  $line1,
        private string  $postalCode,
        private string  $countryCode,
        private string  $countryName,
        private string  $city,
        private ?string $line2 = null,
        private ?string $line3 = null,
        private ?string $company = null,
    )
    {
    }

    public function setLine1(string $line1): void
    {
        $this->line1 = $line1;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function setCountryName(string $countryName): void
    {
        $this->countryName = $countryName;
    }

    public function setLine2(?string $line2): void
    {
        $this->line2 = $line2;
    }

    public function setLine3(?string $line3): void
    {
        $this->line3 = $line3;
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

    public function getCountryName(): string
    {
        return $this->countryName;
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCompany(): ?string
    {
        return $this->company;
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
            'city'         => $this->getCity(),
            'company'      => $this->getCompany(),
        ];
    }
}
