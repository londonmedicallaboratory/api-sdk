<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\Calender;

use DateTimeInterface;

class Slot implements SlotInterface
{
    public function __construct(
        protected DateTimeInterface $time,
        protected ?SlotInterface    $previous = null,
        protected ?SlotInterface    $next = null,
    )
    {
    }

    public function matches(int $hour, int $minute): bool
    {
        $match = sprintf('%02d:%02d', $hour, $minute);

        return $this->format('H:i') === $match;
    }

    public function getPrevious(): ?SlotInterface
    {
        return $this->previous;
    }

    public function getNext(): ?SlotInterface
    {
        return $this->next;
    }

    public function getTime(): DateTimeInterface
    {
        return $this->time;
    }

    public function format(?string $format = null): string
    {
        return $this->getTime()->format($format ?? 'Y-m-d H:i');
    }
}
