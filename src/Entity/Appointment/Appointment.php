<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Appointment;

use DateTimeInterface;
use JetBrains\PhpStorm\ExpectedValues;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\Brand\Brand;
use LML\SDK\Entity\Brand\Calender\Slot;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Exception\EntityNotPersistedException;
use LML\SDK\Repository\AppointmentRepository;
use LML\SDK\Struct\Point;
use LML\View\Lazy\LazyValueInterface;
use LML\View\Lazy\ResolvedValue;

/**
 * @psalm-type TType = 'brand_location'|'home_visit_phlebotomist'|'video'
 *
 * @template TBrand of Brand
 * @template TProduct of Product
 * @template TPatient of Patient
 * @template TSlot of Slot
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     type: TType,
 *     test_location_id: string,
 *     brand_id: string,
 *     slot_id?: ?string,
 *     starts_at: string,
 *     ends_at?: ?string,
 *     patient_id: ?string,
 *     confirmed?: ?bool,
 *     expires_at?: ?string,
 *     time_id?: ?string,
 *     full_address?: ?string,
 *     point?: array{latitude: float, longitude: float},
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: AppointmentRepository::class, baseUrl: 'appointment')]
class Appointment implements ModelInterface
{
    /**
     * @param TType $type
     * @param LazyValueInterface<TBrand> $brand
     * @param LazyValueInterface<TBrand> $location
     * @param LazyValueInterface<list<TProduct>> $products
     * @param LazyValueInterface<DateTimeInterface> $startsAt
     * @param LazyValueInterface<?DateTimeInterface> $endsAt
     * @param LazyValueInterface<?TPatient> $patient
     * @param LazyValueInterface<bool> $isConfirmed
     * @param LazyValueInterface<?string> $timeId
     * @param LazyValueInterface<?DateTimeInterface> $expiresAt
     * @param LazyValueInterface<?string> $fullAddress
     * @param LazyValueInterface<?Point> $point
     * @param LazyValueInterface<?TSlot> $slot
     * @see AppointmentRepository::one()
     *
     */
    public function __construct(
        #[ExpectedValues(values: ['brand_location', 'home_visit_phlebotomist', 'video'])]
        protected string $type,
        protected LazyValueInterface $brand,
        protected LazyValueInterface $location,
        protected LazyValueInterface $startsAt,
        protected LazyValueInterface $endsAt = new ResolvedValue(null),
        protected LazyValueInterface $products = new ResolvedValue([]),
        protected LazyValueInterface $patient = new ResolvedValue(null),
        protected LazyValueInterface $isConfirmed = new ResolvedValue(false),
        protected LazyValueInterface $timeId = new ResolvedValue(null),
        protected LazyValueInterface $expiresAt = new ResolvedValue(null),
        protected LazyValueInterface $fullAddress = new ResolvedValue(null),
        protected LazyValueInterface $point = new ResolvedValue(null),
        protected LazyValueInterface $slot = new ResolvedValue(null),
        protected ?string $id = null,
    )
    {
    }

    /**
     * @return TType
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param TType $type
     */
    public function setType(#[ExpectedValues(values: ['brand_location', 'home_visit_phlebotomist', 'video'])] string $type): void
    {
        $this->type = $type;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt->getValue();
    }

    public function getFullAddress(): ?string
    {
        return $this->fullAddress->getValue();
    }

    public function getPoint(): ?Point
    {
        return $this->point->getValue();
    }

    public function getBrand(): Brand
    {
        return $this->brand->getValue();
    }

    public function getLocation(): Brand
    {
        return $this->location->getValue();
    }

    /**
     * @return list<TProduct>
     */
    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    /**
     * @param TBrand $location
     */
    public function setLocation(Brand $location): void
    {
        $this->location = new ResolvedValue($location);
    }

    /**
     * @return ?TPatient
     */
    public function getPatient(): ?Patient
    {
        return $this->patient->getValue();
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed->getValue();
    }

    public function getId(): string
    {
        return $this->id ?? throw new EntityNotPersistedException();
    }

    public function getTimeId(): ?string
    {
        return $this->timeId->getValue();
    }

    /**
     * @deprecated
     */
    public function getAppointmentTime(): DateTimeInterface
    {
        return $this->getStartsAt();
    }

    /**
     * @return ?TSlot
     */
    public function getSlot(): ?Slot
    {
        return $this->slot->getValue();
    }

    /**
     * @param ?TSlot $slot
     */
    public function setSlot(?Slot $slot): void
    {
        $this->slot = new ResolvedValue($slot);
    }

    public function getStartsAt(): DateTimeInterface
    {
        return $this->startsAt->getValue();
    }

    public function setStartsAt(DateTimeInterface $startsAt): void
    {
        $this->startsAt = new ResolvedValue($startsAt);
    }

    public function getEndsAt(): ?DateTimeInterface
    {
        return $this->endsAt->getValue();
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'test_location_id' => $this->getLocation()->getId(),
            'brand_id' => $this->getBrand()->getId(),
            'starts_at' => $this->getStartsAt()->format('Y-m-d\TH:i:sP'),
            'ends_at' => $this->getEndsAt()?->format('Y-m-d\TH:i:sP'),
            'patient_id' => $this->getPatient()?->getId(),
            'confirmed' => $this->isConfirmed(),
            'time_id' => $this->getTimeId(),
        ];

        if ($slot = $this->getSlot()) {
            $data['slot_id'] = $slot->getId();
        }

        return $data;
    }
}
