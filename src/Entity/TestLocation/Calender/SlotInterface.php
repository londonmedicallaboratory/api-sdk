<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\Calender;

use DateTimeInterface;

/**
 * Doubly-linked
 */
interface SlotInterface
{
    public function getPrevious(): ?SlotInterface;

    public function getNext(): ?SlotInterface;

    public function getTime(): DateTimeInterface;

    public function format(string $format): string;

    public function matches(int $hour, int $minute): bool;
}
