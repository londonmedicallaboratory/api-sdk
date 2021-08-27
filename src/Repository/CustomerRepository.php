<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Model\Customer\Customer;
use LML\SDK\Model\Customer\CustomerInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;

/**
 * @psalm-import-type S from CustomerInterface
 *
 * @extends AbstractViewRepository<S, CustomerInterface, array{product_id?: string}>
 *
 * @see CustomerInterface
 */
class CustomerRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $id = $entity['id'];

        return new Customer(
            id: $id,
            firstName: $entity['first_name'],
            lastName: $entity['last_name'],
            email: $entity['email'],
            phoneNumber: $entity['phone_number'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/files';
    }
}
