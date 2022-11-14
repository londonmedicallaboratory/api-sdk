<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Enum\EthnicityEnum;
use LML\View\Lazy\ResolvedValue;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\Address\Address;
use React\Promise\PromiseInterface;
use LML\SDK\Service\API\AbstractRepository;

/**
 * @psalm-import-type S from Patient
 *
 * @extends AbstractRepository<S, Patient, array>
 */
class PatientRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Patient
    {
        $id = $entity['id'];
        $gender = $entity['gender'];
        $ethnicity = $entity['ethnicity'] ?? '';
        $addressId = $entity['address_id'] ?? null;

        return new Patient(
            id: $id,
            firstName: $entity['first_name'],
            lastName: $entity['last_name'],
            gender: GenderEnum::from($gender),
            dateOfBirth: new DateTime($entity['date_of_birth']),
            ethnicity: EthnicityEnum::tryFrom($ethnicity),
            email: $entity['email'] ?? null,
            foreignId: $entity['foreign_id'] ?? null,
            phoneNumber: $entity['phone_number'] ?? null,
            address: $addressId && $id ? new LazyPromise($this->getAddress($id)) : new ResolvedValue(null),
        );
    }

    /**
     * @return PromiseInterface<?Address>
     */
    private function getAddress(string $id): PromiseInterface
    {
        $url = sprintf('/patient/%s/address', $id);

        return $this->get(AddressRepository::class)->find(url: $url);
    }
}
