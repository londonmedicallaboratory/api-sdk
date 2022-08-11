<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Entity\Address\Address;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Address\AddressInterface;

/**
 * @psalm-import-type S from AddressInterface
 *
 * @extends AbstractRepository<S, AddressInterface, array>
 */
class AddressRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Address
    {
        $id = $entity['id'];

        return new Address(
            id         : $id,
            line1      : $entity['line1'],
            line2      : $entity['line2'] ?? null,
            line3      : $entity['line3'] ?? null,
            postalCode : $entity['postal_code'],
            countryCode: $entity['country_code'],
            countryName: $entity['country_name'] ?? $entity['country_code'],
            city       : $entity['city'],
            company    : $entity['company'] ?? null,
        );
    }
}
