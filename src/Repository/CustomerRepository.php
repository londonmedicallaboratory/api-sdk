<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
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

        return new Customer(
            id         : $id,
            firstName  : $entity['first_name'],
            lastName   : $entity['last_name'],
            email      : $entity['email'],
            phoneNumber: $entity['phone_number'],
            dateOfBirth: new DateTime($entity['date_of_birth']),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/files';
    }
}
