<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\Calender;

use DateTime;
use DateTimeInterface;

class Slot implements SlotInterface
{
    public function __construct(
        protected string $id,
        protected DateTimeInterface $time,
        private bool $isAvailable,
        protected ?SlotInterface $previous = null,
        protected ?SlotInterface $next = null,
    )
    {
    }

    /**
     * @return array{
     *     id: string,
     *     available: bool,
     *     time: string,
     *     preview: string,
     *     is_past: bool,
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'preview' => $this->format('H:i'),
            'available' => $this->isAvailable(),
            'time' => $this->time->format('Y-m-d H:i'),
            'is_past' => $this->isInPast(),
        ];
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

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function isInPast(): bool
    {
        $now = new DateTime();

        return $this->time < $now;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
