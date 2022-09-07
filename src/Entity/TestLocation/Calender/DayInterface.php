<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\Calender;

interface DayInterface
{
    public function format(): string;

    public function isAvailable(): bool;

    /**
     * @return list<SlotInterface>
     */
    public function getAvailableSlots();
}
