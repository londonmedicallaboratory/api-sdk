<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use RuntimeException;
use LML\SDK\DTO\Payment;
use LML\SDK\Entity\Address\Address;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Address
 *
 * @extends AbstractRepository<S, Address, array>
 */
class AddressRepository extends AbstractRepository
{
    public function createFromPayment(Payment $payment): Address
    {
        $deliveryLine1 = $payment->deliveryAddressLine1 ?? $payment->customersAddressLine1;
        $postalCode = $payment->deliveryPostalCode ?? $payment->customersPostalCode;

        return new Address(
            id: '',
            line1: $deliveryLine1 ?? throw new RuntimeException(),
            postalCode: $postalCode ?? throw new RuntimeException(),
            city: '',
            countryCode: 'GB',
            countryName: 'GB',
            line2: $payment->deliveryAddressLine2 ?? $payment->customersAddressLine2,
            line3: $payment->deliveryAddressLine3 ?? $payment->customersAddressLine3,
        );
    }

    protected function one($entity, $options, $optimizer): Address
    {
        $id = $entity['id'];

        return new Address(
            id: $id,
            line1: $entity['line1'],
            line2: $entity['line2'] ?? null,
            line3: $entity['line3'] ?? null,
            postalCode: $entity['postal_code'],
            countryCode: $entity['country_code'],
            countryName: $entity['country_name'] ?? $entity['country_code'],
            city: $entity['city'],
            company: $entity['company'] ?? null,
        );
    }
}
