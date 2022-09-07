<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\Calender;

use DateTimeInterface;

class Slot implements SlotInterface
{
    public function __construct(
        private DateTimeInterface $time,
        private ?SlotInterface    $previous = null,
        private ?SlotInterface    $next = null,
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

    public function format(string $format): string
    {
        return $this->getTime()->format($format);
    }
}
