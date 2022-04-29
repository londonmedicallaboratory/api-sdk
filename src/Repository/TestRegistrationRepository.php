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
use LML\SDK\Entity\TestRegistration\TestRegistration;
use LML\SDK\Entity\TestRegistration\TestRegistrationInterface;
use function sprintf;

/**
 * @psalm-import-type S from TestRegistrationInterface
 *
 * @extends AbstractRepository<S, TestRegistrationInterface, array>
 */
class TestRegistrationRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): TestRegistration
    {
        $createdAt = $entity['created_at'] ?? null;
        $completedAt = $entity['completed_at'] ?? null;
        $dateOfArrival = $entity['date_of_arrival'] ?? null;
        $id = $entity['id'];

        return new TestRegistration(
            products     : new LazyPromise($this->getProducts($id)),
            patient      : new LazyPromise($this->getPatient($id)),
            dateOfArrival: $dateOfArrival ? new DateTime($dateOfArrival) : null,
            resultsReady : new ResolvedValue($entity['results_ready']),
            createdAt    : $createdAt ? new DateTime($createdAt) : new DateTime(),
            completedAt  : $completedAt ? new DateTime($completedAt) : null,
            doctorsNote  : $entity['doctors_note'] ?? null,
            id           : $id,
        );
    }

    protected function getBaseUrl(): string
    {
        return '/test_registration';
    }

    /**
     * @return PromiseInterface<Patient>
     */
    private function getPatient(string $id): PromiseInterface
    {
        $url = sprintf('/test_registration/%s/patient', $id);

        return $this->get(PatientRepository::class)->fetchOneBy(url: $url);
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
