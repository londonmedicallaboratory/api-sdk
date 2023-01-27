<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\Calender;

use RuntimeException;
use DateTimeInterface;

/**
 * @template T of SlotInterface
 *
 * @implements DayInterface<T>
 */
class Day implements DayInterface
{
    public function getSlots(?DateTimeInterface $after = null): array
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
