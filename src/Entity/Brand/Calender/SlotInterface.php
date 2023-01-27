<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\Calender;

use DateTimeInterface;

/**
 * Doubly-linked
 */
interface SlotInterface
{
    public function getPrevious(): ?SlotInterface;

    public function getNext(): ?SlotInterface;

    public function getTime(): DateTimeInterface;

    public function format(?string $format = null): string;

    public function matches(int $hour, int $minute): bool;
}
