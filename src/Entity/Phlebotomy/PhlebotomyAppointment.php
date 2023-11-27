<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Phlebotomy;

use DateTimeInterface;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\Phlebotomy\PhlebotomyAppointmentRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      starts_at: string,
 *      ends_at: string,
 *      display_value: string,
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: PhlebotomyAppointmentRepository::class, baseUrl: 'phlebotomy')]
class PhlebotomyAppointment implements ModelInterface
{
    public function __construct(
        private string $id,
        private DateTimeInterface $startsAt,
        private DateTimeInterface $endsAt,
        private string $displayValue,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStartsAt(): DateTimeInterface
    {
        return $this->startsAt;
    }

    public function getEndsAt(): DateTimeInterface
    {
        return $this->endsAt;
    }

    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'starts_at' => $this->getStartsAt()->format('Y-m-d\TH:i:sP'),
            'ends_at' => $this->getEndsAt()->format('Y-m-d\TH:i:sP'),
            'display_value' => $this->getDisplayValue(),
        ];
    }
}
