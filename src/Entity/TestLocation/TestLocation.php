<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\TestLocationRepository;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessionalInterface;

#[Entity(repositoryClass: TestLocationRepository::class, baseUrl: 'test_location')]
class TestLocation implements TestLocationInterface
{
    /**
     * @param LazyValueInterface<list<HealthcareProfessionalInterface>> $healthcareProfessionals,
     */
    public function __construct(
        protected string $id,
        protected string $fullAddress,
        protected string $city,
        protected string $postalCode,
        protected string $name,
        private LazyValueInterface $healthcareProfessionals,
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

    public function getHealthcareProfessionals()
    {
        return $this->healthcareProfessionals->getValue();
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
