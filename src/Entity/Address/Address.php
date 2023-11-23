<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Address;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\AddressRepository;
use LML\SDK\Exception\EntityNotPersistedException;
use function implode;
use function array_filter;

/**
 * @psalm-type S=array{
 *      id?: ?string,
 *      line1: string,
 *      line2?: ?string,
 *      line3?: ?string,
 *      postal_code: string,
 *      country_name?: string,
 *      country_code: string,
 *      city: string,
 *      company?: ?string,
 *      state?: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: AddressRepository::class, baseUrl: 'address')]
class Address implements ModelInterface, Stringable
{
    public function __construct(
        private string $line1,
        private string $postalCode,
        private string $countryCode,
        private string $countryName,
        private string $city,
        private ?string $state = null,
        private ?string $id = null,
        private ?string $line2 = null,
        private ?string $line3 = null,
        private ?string $company = null,
    )
    {
    }

    public function __toString(): string
    {
        $parts = [
            $this->getLine1(),
            $this->getLine2(),
            $this->getLine3(),
            $this->getCity(),
            $this->getState(),
            $this->getCountryCode(),
            $this->getPostalCode(),
        ];
        $filtered = array_filter($parts, static fn(?string $part) => (bool)$part);

        return implode(', ', $filtered);
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
        return $this->id ?? throw new EntityNotPersistedException();
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

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'line1' => $this->getLine1(),
            'line2' => $this->getLine2(),
            'line3' => $this->getLine3(),
            'postal_code' => $this->getPostalCode(),
            'country_name' => $this->getCountryName(),
            'country_code' => $this->getCountryCode(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'company' => $this->getCompany(),
        ];
    }
}
