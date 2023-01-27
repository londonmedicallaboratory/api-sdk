<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\Calender;

use DateTimeInterface;

/**
 * @template T of SlotInterface
 */
interface DayInterface
{
    public function format(): string;

    public function isAvailable(): bool;

    /**
     * @return list<T>
     */
    public function getSlots(?DateTimeInterface $after = null);
}
