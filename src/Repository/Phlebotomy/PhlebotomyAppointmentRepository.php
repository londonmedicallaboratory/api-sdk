<?php

declare(strict_types=1);

namespace LML\SDK\Repository\Phlebotomy;

use DateTimeImmutable;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Phlebotomy\PhlebotomyAppointment;

/**
 * @psalm-import-type S from PhlebotomyAppointment
 *
 * @extends AbstractRepository<S, PhlebotomyAppointment, array{
 *     postal_code: string,
 * }>
 */
class PhlebotomyAppointmentRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): PhlebotomyAppointment
    {
        return new PhlebotomyAppointment(
            id: $entity['id'],
            startsAt: new DateTimeImmutable($entity['starts_at']),
            endsAt: new DateTimeImmutable($entity['ends_at']),
        );
    }
}
