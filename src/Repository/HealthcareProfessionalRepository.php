<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\ViewFactory\AbstractViewRepository;
use LML\SDK\Model\HealthcareProfessional\HealthcareProfessional;
use LML\SDK\Model\HealthcareProfessional\HealthcareProfessionalInterface;

/**
 * @psalm-import-type S from HealthcareProfessionalInterface
 * @extends AbstractViewRepository<S, HealthcareProfessional, array>
 *
 * @see HealthcareProfessionalInterface
 */
class HealthcareProfessionalRepository extends AbstractViewRepository
{
    protected function one($entity, $options, $optimizer): HealthcareProfessional
    {
        return new HealthcareProfessional(
            id: $entity['id'],
            firstName: $entity['first_name'],
            lastName: $entity['last_name'],
            isNurse: $entity['is_nurse'],
            isLMLApproved: $entity['is_lml_approved'],
        );
    }

    protected function getBaseUrl(): string
    {
        return '/healthcare_professional';
    }
}
