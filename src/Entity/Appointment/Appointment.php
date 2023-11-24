<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Appointment;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\Brand\Brand;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\View\Lazy\LazyValueInterface;
use JetBrains\PhpStorm\ExpectedValues;
use LML\SDK\Repository\AppointmentRepository;
use LML\SDK\Exception\EntityNotPersistedException;

/**
 * @psalm-type TType = 'brand_location'|'home_visit_phlebotomist'
 *
 * @template TBrand of Brand
 * @template TProduct of Product
 * @template TPatient of Patient
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     type: TType,
 *     brand_id: string,
 *     starts_at: string,
 *     ends_at?: ?string,
 *     patient_id: ?string,
 *     confirmed?: ?bool,
 *     time_id?: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: AppointmentRepository::class, baseUrl: 'appointment')]
class Appointment implements ModelInterface
{
    /**
     * @see AppointmentRepository::one()
     *
     * @param TType $type
     * @param LazyValueInterface<TBrand> $brand
     * @param LazyValueInterface<list<TProduct>> $products
     * @param LazyValueInterface<DateTimeInterface> $startsAt
     * @param LazyValueInterface<?DateTimeInterface> $endsAt
     * @param LazyValueInterface<?TPatient> $patient
     * @param LazyValueInterface<bool> $isConfirmed
     * @param LazyValueInterface<?string> $timeId
     */
    public function __construct(
        #[ExpectedValues(values: ['brand_location', 'home_visit_phlebotomist'])]
        protected string $type,
        protected LazyValueInterface $brand,
        protected LazyValueInterface $startsAt,
        protected LazyValueInterface $endsAt = new ResolvedValue(null),
        protected LazyValueInterface $products = new ResolvedValue([]),
        protected LazyValueInterface $patient = new ResolvedValue(null),
        protected LazyValueInterface $isConfirmed = new ResolvedValue(false),
        protected LazyValueInterface $timeId = new ResolvedValue(null),
        protected ?string $id = null,
    )
    {
    }

    public function getBrand(): Brand
    {
        return $this->brand->getValue();
    }

    /**
     * @return list<TProduct>
     */
    public function getProducts(): array
    {
        return $this->products->getValue();
    }

    /**
     * @param TBrand $brand
     */
    public function setBrand(Brand $brand): void
    {
        $this->brand = new ResolvedValue($brand);
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
        return [
            'id' => $this->id,
            'type' => $this->type,
            'brand_id' => $this->getBrand()->getId(),
            'starts_at' => $this->getStartsAt()->format('Y-m-d\TH:i:sP'),
            'ends_at' => $this->getEndsAt()?->format('Y-m-d\TH:i:sP'),
            'patient_id' => $this->getPatient()?->getId(),
            'confirmed' => $this->isConfirmed(),
            'time_id' => $this->getTimeId(),
        ];
    }
}
