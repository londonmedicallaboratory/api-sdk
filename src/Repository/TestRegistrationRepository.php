<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Model\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\TestRegistration\TestRegistration;
use LML\SDK\Model\TestRegistration\TestRegistrationInterface;

/**
 * @psalm-import-type S from TestRegistrationInterface
 *
 * @extends AbstractViewRepository<S, TestRegistrationInterface, array>
 */
class TestRegistrationRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer): TestRegistration
    {
        /** @var GenderEnum::* $gender */
        $gender = $entity['gender'];
        /** @var EthnicityEnum::* $ethnicity */
        $ethnicity = $entity['ethnicity'];

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
            isVaccinated: $entity['is_vaccinated'],
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
