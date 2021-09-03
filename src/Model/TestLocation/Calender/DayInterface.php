<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\Calender;

interface DayInterface
{
    public function format(): string;

    public function isAvailable(): bool;

    /**
     * @return list<string>
     */
    public function getAvailableSlots();
}
