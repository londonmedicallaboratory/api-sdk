<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\Calender;

use DateTimeInterface;

interface DayInterface
{
    public function format(): string;

    public function isAvailable(): bool;

    /**
     * @return list<DateTimeInterface>
     */
    public function getAvailableSlots();
}
