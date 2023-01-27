<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\WorkingHours;

use LML\SDK\Enum\DayOfWeekEnum;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *     id: string,
 *     day_of_week: string,
 *     starts_at: string,
 *     ends_at: string,
 *     active?: bool,
 * }
 *
 * @implements ModelInterface<S>
 */
class WorkingHours implements ModelInterface
{
    public function __construct(
        protected string $id,
        protected DayOfWeekEnum $dayOfWeek,
        protected string $startsAt,
        protected string $endsAt,
        protected bool $isActive = true,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDayOfWeek(): DayOfWeekEnum
    {
        return $this->dayOfWeek;
    }

    public function getStartsAt(): string
    {
        return $this->startsAt;
    }

    public function getEndsAt(): string
    {
        return $this->endsAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'day_of_week' => $this->getDayOfWeek()->getShortcut(),
            'starts_at' => $this->getStartsAt(),
            'ends_at' => $this->getEndsAt(),
            'active' => $this->isActive(),
        ];
    }
}
