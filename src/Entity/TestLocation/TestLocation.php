<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\TestLocationRepository;
use LML\SDK\Entity\TestLocation\WorkingHours\WorkingHoursInterface;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessionalInterface;

#[Entity(repositoryClass: TestLocationRepository::class, baseUrl: 'test_location')]
class TestLocation implements TestLocationInterface
{
    /**
     * @param LazyValueInterface<list<HealthcareProfessionalInterface>> $healthcareProfessionals ,
     * @param LazyValueInterface<list<WorkingHoursInterface>> $workHours ,
     */
    public function __construct(
        protected string           $id,
        protected string           $fullAddress,
        protected string           $city,
        protected string           $postalCode,
        protected string           $name,
        private LazyValueInterface $healthcareProfessionals,
        private LazyValueInterface $workHours,
        protected ?string          $nearestBusStation = null,
        protected ?string          $nearestTrainStation = null,
    )
    {
    }

    public function getNearestBusStation(): ?string
    {
        return $this->nearestBusStation;
    }

    public function getNearestTrainStation(): ?string
    {
        return $this->nearestTrainStation;
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
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

    public function getWorkingHours(): array
    {
        return $this->workHours->getValue();
    }

    public function toArray(): array
    {
        return [
            'id'                    => $this->getId(),
            'full_address'          => $this->getFullAddress(),
            'city'                  => $this->getCity(),
            'postal_code'           => $this->getPostalCode(),
            'name'                  => $this->getName(),
            'nearest_bus_station'   => $this->getNearestBusStation(),
            'nearest_train_station' => $this->getNearestTrainStation(),
        ];
    }
}
