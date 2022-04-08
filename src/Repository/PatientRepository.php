<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use DateTime;
use LML\SDK\Enum\GenderEnum;
use LML\SDK\Enum\EthnicityEnum;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Patient\PatientInterface;

/**
 * @psalm-import-type S from PatientInterface
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

        return new Patient(
            id         : $id,
            firstName  : $entity['first_name'],
            lastName   : $entity['last_name'],
            gender     : GenderEnum::from($gender),
            dateOfBirth: new DateTime($entity['date_of_birth']),
            ethnicity  : EthnicityEnum::tryFrom($ethnicity),
            email      : $entity['email'] ?? null,
        );
    }

    protected function getBaseUrl(): string
    {
        return '/patient';
    }
}
