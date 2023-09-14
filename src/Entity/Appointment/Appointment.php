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
use LML\SDK\Repository\AppointmentRepository;
use LML\SDK\Exception\EntityNotPersistedException;

/**
 * @template TBrand of Brand
 * @template TProduct of Product
 * @template TPatient of Patient
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     brand_id: string,
 *     appointment_time: string,
 *     patient_id: ?string,
 *     confirmed?: ?bool,
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
     * @param LazyValueInterface<TBrand> $brand
     * @param LazyValueInterface<list<TProduct>> $products
     * @param LazyValueInterface<DateTimeInterface> $appointmentTime
     * @param LazyValueInterface<?TPatient> $patient
     * @param LazyValueInterface<bool> $isConfirmed
     */
    public function __construct(
        protected LazyValueInterface $brand,
        protected LazyValueInterface $appointmentTime,
        protected LazyValueInterface $products = new ResolvedValue([]),
        protected LazyValueInterface $patient = new ResolvedValue(null),
        protected LazyValueInterface $isConfirmed = new ResolvedValue(false),
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

    public function getAppointmentTime(): DateTimeInterface
    {
        return $this->appointmentTime->getValue();
    }

    public function setAppointmentTime(DateTimeInterface $appointmentTime): void
    {
        $this->appointmentTime = new ResolvedValue($appointmentTime);
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->getBrand()->getId(),
            'appointment_time' => $this->getAppointmentTime()->format('Y-m-d\TH:i:sP'),
            'patient_id' => $this->getPatient()?->getId(),
            'confirmed' => $this->isConfirmed(),
        ];
    }
}
