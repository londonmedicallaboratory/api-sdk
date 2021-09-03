<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\Calender;

use RuntimeException;

class Day implements DayInterface
{
    public function getAvailableSlots()
    {
        return [];
    }

    public function format(): string
    {
        throw new RuntimeException();
    }

    public function isAvailable(): bool
    {
        throw new RuntimeException();
    }
}
