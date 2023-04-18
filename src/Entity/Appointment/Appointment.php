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
 * @template TLoc of Brand
 *
 * @psalm-type Item = array{product_id: string, quantity: int, product_sku?: ?string}
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     brand_id: string,
 *     appointment_time: string,
 *     product_id: ?string,
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
     * @param LazyValueInterface<TLoc> $brand
     * @param LazyValueInterface<DateTimeInterface> $appointmentTime
     * @param LazyValueInterface<?Product> $product
     * @param LazyValueInterface<?Patient> $patient
     * @param LazyValueInterface<bool> $isConfirmed
     */
    public function __construct(
        protected LazyValueInterface $brand,
        protected LazyValueInterface $appointmentTime,
        protected LazyValueInterface $product,
        protected LazyValueInterface $patient,
        protected LazyValueInterface $isConfirmed,
        protected ?string $id = null,
    )
    {
    }

    public function getBrand(): Brand
    {
        return $this->brand->getValue();
    }

    public function getAppointmentTime(): DateTimeInterface
    {
        return $this->appointmentTime->getValue();
    }

    public function setAppointmentTime(DateTimeInterface $appointmentTime): void
    {
        $this->appointmentTime = new ResolvedValue($appointmentTime);
    }

    public function getProduct(): ?Product
    {
        return $this->product->getValue();
    }

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
            'product_id' => $this->getProduct()?->getId(),
            'patient_id' => $this->getPatient()?->getId(),
            'confirmed' => $this->isConfirmed(),
        ];
    }
}
