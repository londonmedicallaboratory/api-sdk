<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\WeeklyWorkingHours;

class WeeklyHoursPeriod
{
    public function __construct(
        private string $daysPeriod,
        private string $hoursPeriod,
    )
    {
    }

    public function getDaysPeriod(): string
    {
        return $this->daysPeriod;
    }

    public function getHoursPeriod(): string
    {
        return $this->hoursPeriod;
    }
}
