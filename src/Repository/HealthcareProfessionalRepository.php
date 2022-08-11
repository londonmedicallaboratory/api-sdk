<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessional;
use LML\SDK\Entity\HealthcareProfessional\HealthcareProfessionalInterface;

/**
 * @psalm-import-type S from HealthcareProfessionalInterface
 *
 * @extends AbstractRepository<S, HealthcareProfessional, array>
 */
class HealthcareProfessionalRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): HealthcareProfessional
    {
        return new HealthcareProfessional(
            id           : $entity['id'],
            firstName    : $entity['first_name'],
            lastName     : $entity['last_name'],
            isNurse      : $entity['is_nurse'],
            isLMLApproved: $entity['is_lml_approved'],
        );
    }
}
