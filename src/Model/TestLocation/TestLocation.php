<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation;

use LML\SDK\Attribute\Model;
use LML\SDK\Repository\TestLocationRepository;

#[Model(repositoryClass: TestLocationRepository::class)]
class TestLocation implements TestLocationInterface
{
    public function __construct(
        private string $id,
        private string $fullAddress,
        private string $city,
        private string $postalCode,
        private string $name,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullAddress(): string
    {
        return $this->fullAddress;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray()
    {
        return [
            'id'           => $this->getId(),
            'full_address' => $this->getFullAddress(),
            'city'         => $this->getCity(),
            'postal_code'  => $this->getPostalCode(),
            'name'         => $this->getName(),
        ];
    }
}
