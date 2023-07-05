<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand;

use Stringable;
use LML\SDK\Struct\Point;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Entity\Brand\Calender\Slot;
use LML\SDK\Entity\Brand\WorkingHours\WorkingHours;
use LML\SDK\Entity\Brand\WeeklyWorkingHours\WeeklyWorkingHours;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessional;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      full_address: string,
 *      city: string,
 *      postal_code: string,
 *      nearest_bus_station?: ?string,
 *      nearest_train_station?: ?string,
 *      next_available_slot?: ?string,
 *      latitude?: ?float,
 *      longitude?: ?float,
 *      distance?: ?int,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: BrandRepository::class, baseUrl: 'test_location')]
class Brand implements ModelInterface, Stringable
{
    /**
     * @param LazyValueInterface<list<HealthcareProfessional>> $healthcareProfessionals
     * @param LazyValueInterface<list<WorkingHours>> $workHours
     * @param LazyValueInterface<?Slot> $nextAvailableSlot
     */
    public function __construct(
        protected string $id,
        protected string $fullAddress,
        protected string $city,
        protected string $postalCode,
        protected string $name,
        private LazyValueInterface $healthcareProfessionals,
        private LazyValueInterface $workHours,
        protected LazyValueInterface $nextAvailableSlot,
        protected ?string $nearestBusStation = null,
        protected ?string $nearestTrainStation = null,
        private ?Point $point = null,
        protected ?int $distance = null,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
    }

    public function getNextAvailableSlot(): ?Slot
    {
        return $this->nextAvailableSlot->getValue();
    }

    public function getNearestBusStation(): ?string
    {
        return $this->nearestBusStation;
    }

    public function getNearestTrainStation(): ?string
    {
        return $this->nearestTrainStation;
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

    /**
     * @return list<HealthcareProfessional>
     */
    public function getHealthcareProfessionals()
    {
        return $this->healthcareProfessionals->getValue();
    }

    /**
     * @return list<WorkingHours>
     */
    public function getWorkingHours(): array
    {
        return $this->workHours->getValue();
    }

    public function getWeeklyWorkingHours(): WeeklyWorkingHours
    {
        return new WeeklyWorkingHours($this->getWorkingHours());
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'full_address' => $this->getFullAddress(),
            'city' => $this->getCity(),
            'postal_code' => $this->getPostalCode(),
            'name' => $this->getName(),
            'nearest_bus_station' => $this->getNearestBusStation(),
            'nearest_train_station' => $this->getNearestTrainStation(),
            'next_available_slot' => $this->getNextAvailableSlot()?->format('Y-m-d H:i'),
        ];
    }
}
