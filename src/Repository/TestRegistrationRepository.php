<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\TestRegistration\TestRegistration;
use LML\SDK\Entity\TestRegistration\TestRegistrationStatusEnum;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Service\API\AbstractRepository;
use LML\View\Lazy\ResolvedValue;
use React\Promise\PromiseInterface;
use function React\Async\await;
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
    public function getPersistenceGraph(ModelInterface $view): iterable
    {
        yield from $view->getProducts();
        yield $view->getPatient();
        yield $view->getAppointment();
    }

    public function sendCompleteEmail(TestRegistration $testRegistration, string $to): void
    {
        await($this->getClient()->post(sprintf('/test_registration/%s/send-complete-email', $testRegistration->getId()), ['email' => $to]));
    }

    protected function one($entity, $options, $optimizer): TestRegistration
    {
        $createdAt = $entity['created_at'] ?? null;
        $completedAt = $entity['completed_at'] ?? null;
        $patientRegisteredAt = $entity['patient_registered_at'] ?? null;
        $id = $entity['id'];

        return new TestRegistration(
            products: new LazyPromise($this->getProducts($id)),
            patient: new LazyPromise($this->getPatient($entity['patient_id'] ?? null)),
            downloadUrl: new ResolvedValue($entity['download_url'] ?? null),
            trfCode: new ResolvedValue($entity['trf_code'] ?? null),
            status: TestRegistrationStatusEnum::from($entity['status']),
            resultsReady: new ResolvedValue($entity['results_ready']),
            createdAt: $createdAt ? new DateTime($createdAt) : new DateTime(),
            completedAt: $completedAt ? new DateTime($completedAt) : null,
            patientRegisteredAt: $patientRegisteredAt ? new DateTime($patientRegisteredAt) : null,
            appointment: new LazyPromise($this->getAppointment($id)),
            doctorsNote: $entity['doctors_note'] ?? null,
            doctorsName: $entity['doctors_name'] ?? null,
            id: $id,
            clinicalDetails: $entity['clinical_details'] ?? null,
            includeHumanityProduct: $entity['include_humanity_product'] ?? false,
        );
    }

    /**
     * @return PromiseInterface<?Appointment>
     */
    private function getAppointment(string $id): PromiseInterface
    {
        $url = sprintf('/test_registration/%s/appointment', $id);

        return $this->get(AppointmentRepository::class)->find(url: $url);
    }

    /**
     * @return PromiseInterface<?Patient>
     */
    private function getPatient(?string $patientId): PromiseInterface
    {
        return $this->get(PatientRepository::class)->find(id: $patientId);
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
