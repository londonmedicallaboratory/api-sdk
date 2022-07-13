<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\WorkingHours;

use LML\SDK\Enum\DayOfWeekEnum;

class WorkingHours implements WorkingHoursInterface
{
    public function __construct(
        protected string        $id,
        protected DayOfWeekEnum $dayOfWeek,
        protected string        $startsAt,
        protected string        $endsAt,
        protected bool          $isActive = true,
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
            'id'          => $this->getId(),
            'day_of_week' => $this->getDayOfWeek()->getShortcut(),
            'starts_at'   => $this->getStartsAt(),
            'ends_at'     => $this->getEndsAt(),
        ];
    }
}
