<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\TestLocation\TestLocation;
use LML\SDK\Entity\Appointment\AppointmentInterface;

/**
 * @psalm-import-type S from AppointmentInterface
 *
 * @extends AbstractRepository<S, Appointment, array>
 */
class AppointmentRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Appointment
    {
        $id = $entity['id'] ?? null;

        return new Appointment(
            id: $id,
            appointmentTime: new ResolvedValue(new DateTime($entity['appointment_time'])),
            testLocation: new LazyPromise($this->getTestLocation($entity['test_location_id'])),
            product: new LazyPromise($this->getProduct($entity['product_id'])),
            patient: new LazyPromise($this->getPatient($entity['patient_id'])),
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
     * @return PromiseInterface<TestLocation>
     */
    private function getTestLocation(string $id): PromiseInterface
    {
        return $this->get(TestLocationRepository::class)->findOrThrowException($id);
    }

    /**
     * @return PromiseInterface<null|Patient>
     */
    private function getPatient(?string $id): PromiseInterface
    {
        return $this->get(PatientRepository::class)->find($id);
    }
}
