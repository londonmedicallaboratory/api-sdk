<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Appointment;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Patient\Patient;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\TestLocation\TestLocation;
use LML\SDK\Repository\AppointmentRepository;
use LML\SDK\Exception\EntityNotPersistedException;

/**
 * @template TLoc of TestLocation
 *
 * @psalm-type Item = array{product_id: string, quantity: int, product_sku?: ?string}
 *
 * @psalm-type S=array{
 *     id?: ?string,
 *     testlocation_id: string,
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
     * @param LazyValueInterface<TLoc> $testLocation
     * @param LazyValueInterface<DateTimeInterface> $appointmentTime
     * @param LazyValueInterface<?Product> $product
     * @param LazyValueInterface<?Patient> $patient
     * @param LazyValueInterface<bool> $isConfirmed
     */
    public function __construct(
        protected LazyValueInterface $testLocation,
        protected LazyValueInterface $appointmentTime,
        protected LazyValueInterface $product,
        protected LazyValueInterface $patient,
        protected LazyValueInterface $isConfirmed,
        protected ?string $id = null,
    )
    {
    }

    public function getTestLocation(): TestLocation
    {
        return $this->testLocation->getValue();
    }

    public function getAppointmentTime(): DateTimeInterface
    {
        return $this->appointmentTime->getValue();
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
            'testlocation_id' => $this->getTestLocation()->getId(),
            'appointment_time' => $this->getAppointmentTime()->format('Y-m-d H:i'),
            'product_id' => $this->getProduct()?->getId(),
            'patient_id' => $this->getPatient()?->getId(),
            'confirmed' => $this->isConfirmed(),
        ];
    }
}
