<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Lazy\LazyPromise;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\TestRegistration\TestRegistration;
use function sprintf;

/**
 * @psalm-import-type S from TestRegistration
 *
 * @extends AbstractRepository<S, TestRegistration, array{
 *     customer?: string,
 *     order?: string,
 * }>
 */
class TestRegistrationRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): TestRegistration
    {
        $createdAt = $entity['created_at'] ?? null;
        $completedAt = $entity['completed_at'] ?? null;
        $patientRegisteredAt = $entity['patient_registered_at'] ?? null;
        $id = $entity['id'];

        return new TestRegistration(
            products: new LazyPromise($this->getProducts($id)),
            patient: new LazyPromise($this->getPatient($id)),
            downloadUrl: new ResolvedValue($entity['download_url'] ?? null),
            trfCode: new ResolvedValue($entity['trf_code'] ?? null),
            resultsReady: new ResolvedValue($entity['results_ready']),
            createdAt: $createdAt ? new DateTime($createdAt) : new DateTime(),
            completedAt: $completedAt ? new DateTime($completedAt) : null,
            patientRegisteredAt: $patientRegisteredAt ? new DateTime($patientRegisteredAt) : null,
            appointment: new LazyPromise($this->getAppointment($id)),
            doctorsNote: $entity['doctors_note'] ?? null,
            doctorsName: $entity['doctors_name'] ?? null,
            id: $id,
        );
    }

    /**
     * @return PromiseInterface<?Appointment>
     */
    private function getAppointment(string $id): PromiseInterface
    {
        $url = sprintf('/test_registration/%s/apppointment', $id);

        return $this->get(AppointmentRepository::class)->findOneBy(url: $url);
    }

    /**
     * @return PromiseInterface<?Patient>
     */
    private function getPatient(string $id): PromiseInterface
    {
        $url = sprintf('/test_registration/%s/patient', $id);

        return $this->get(PatientRepository::class)->findOneBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<Product>>
     */
    private function getProducts(string $id): PromiseInterface
    {
        $url = sprintf('/test_registration/%s/products', $id);

        return $this->get(ProductRepository::class)->findBy(url: $url);
    }
}
