<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Phlebotomy;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\Phlebotomy\PhlebotomyAppointmentRepository;
use function implode;

/**
 * @psalm-type S=array{
 *      starts_at: string,
 *      ends_at: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: PhlebotomyAppointmentRepository::class, baseUrl: 'phlebotomy')]
class PhlebotomyAppointment implements ModelInterface
{
    public function __construct(
        private DateTimeInterface $startsAt,
        private DateTimeInterface $endsAt,
    )
    {
    }

    public function getId(): string
    {
        return implode(',', $this->toArray());
    }

    public function toArray(): array
    {
        return [
            'starts_at' => $this->getStartsAt()->format('Y-m-d\TH:i:sP'),
            'ends_at' => $this->getStartsAt()->format('Y-m-d\TH:i:sP'),
        ];
    }

    public function getStartsAt(): DateTimeInterface
    {
        return $this->startsAt;
    }

    public function getEndsAt(): DateTimeInterface
    {
        return $this->endsAt;
    }
}
