<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use DateTimeInterface;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\Brand\Brand;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;

/**
 * @psalm-import-type S from Appointment
 *
 * @extends AbstractRepository<S, Appointment, array{
 *     start_date?: ?string|DateTimeInterface,
 *     end_date?: ?string|DateTimeInterface,
 * }>
 */
class AppointmentRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Appointment
    {
        $id = $entity['id'] ?? null;

        return new Appointment(
            id: $id,
            appointmentTime: new ResolvedValue(new DateTime($entity['appointment_time'])),
            brand: new LazyPromise($this->getTestLocation($entity['brand_id'])),
            product: new LazyPromise($this->getProduct($entity['product_id'])),
            patient: new LazyPromise($this->getPatient($entity['patient_id'])),
            isConfirmed: new ResolvedValue($entity['confirmed'] ?? false),
        );
    }

    /**
     * @return PromiseInterface<?Product>
     */
    private function getProduct(?string $id): PromiseInterface
    {
        return $this->get(ProductRepository::class)->find($id);
    }

    /**
     * @return PromiseInterface<Brand>
     */
    private function getTestLocation(string $id): PromiseInterface
    {
        return $this->get(BrandRepository::class)->fetch($id);
    }

    /**
     * @return PromiseInterface<null|Patient>
     */
    private function getPatient(?string $id): PromiseInterface
    {
        return $this->get(PatientRepository::class)->find($id);
    }
}
