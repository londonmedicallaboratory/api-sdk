<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Entity\Customer\Customer;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Customer\CustomerInterface;

/**
 * @psalm-import-type S from CustomerInterface
 *
 * @extends AbstractRepository<S, CustomerInterface, array{product_id?: string}>
 *
 * @see CustomerInterface
 */
class CustomerRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Customer
    {
        $id = $entity['id'];
        $dateOfBirth = $entity['date_of_birth'] ?? null;
        $gender = $entity['gender'] ?? '';

        return new Customer(
            id         : $id,
            firstName  : $entity['first_name'],
            lastName   : $entity['last_name'],
            email      : $entity['email'],
            phoneNumber: $entity['phone_number'],
            dateOfBirth: $dateOfBirth ? new DateTime($dateOfBirth) : null,
            nhsNumber  : $entity['nhs_number'],
            gender     : GenderEnum::tryFrom($gender),
            foreignId  : $entity['foreign_id'] ?? null,
        );
    }

    protected function getBaseUrl(): string
    {
        return '/files';
    }
}
