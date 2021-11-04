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
        /** @var null|EthnicityEnum::* $ethnicity */
        $ethnicity = $entity['ethnicity'];
        /** @var  VaccinationStatusEnum::* */
        $vaccinationStatus = $entity['vaccination_status'];

        return new TestRegistration(
            id: $entity['id'],
            product: new LazyPromise($this->getProduct($entity['product_id'])),
            email: $entity['email'],
            dateOfBirth: new DateTime($entity['date_of_birth']),
            firstName: $entity['first_name'],
            lastName: $entity['last_name'],
            gender: $gender,
            ethnicity: $ethnicity,
            mobilePhoneNumber: $entity['mobile_phone_number'],
            passportNumber: $entity['email'],
            nhsNumber: $entity['nhs_number'],
            vaccinationStatus: $vaccinationStatus,
            dateOfArrival: new DateTime(),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/test_registration';
    }

    /**
     * @return PromiseInterface<Product>
     */
    private function getProduct(string $productId): PromiseInterface
    {
        return $this->get(ProductRepository::class)->findOrThrowException($productId);
    }
}
