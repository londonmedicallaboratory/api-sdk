<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Model\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\Enum\VaccinationStatusEnum;
use LML\SDK\Service\Model\AbstractRepository;
use LML\SDK\Model\TestRegistration\TestRegistration;
use LML\SDK\Model\TestRegistration\TestRegistrationInterface;
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
        /** @var GenderEnum::* $gender */
        $gender = $entity['gender'];
        $ethnicity = $entity['ethnicity'] ?? '';
        /** @var ?VaccinationStatusEnum::* */
        $vaccinationStatus = $entity['vaccination_status'] ?? '';
        $createdAt = $entity['created_at'] ?? null;
        $completedAt = $entity['completed_at'] ?? null;
        $dateOfArrival = $entity['date_of_arrival'] ?? null;

        return new TestRegistration(
            products         : new LazyPromise($this->getProducts($entity['id'])),
            email            : $entity['email'],
            dateOfBirth      : new DateTime($entity['date_of_birth']),
            firstName        : $entity['first_name'],
            lastName         : $entity['last_name'],
            gender           : $gender,
            ethnicity        : EthnicityEnum::from($ethnicity),
            mobilePhoneNumber: $entity['mobile_phone_number'],
            passportNumber   : $entity['email'],
            nhsNumber        : $entity['nhs_number'] ?? null,
            vaccinationStatus: $vaccinationStatus,
            dateOfArrival    : $dateOfArrival ? new DateTime($dateOfArrival) : null,
            resultsReady     : $entity['results_ready'],
            createdAt        : $createdAt ? new DateTime($createdAt) : new DateTime(),
            completedAt      : $completedAt ? new DateTime($completedAt) : null,
            id               : $entity['id'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/test_registration';
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
