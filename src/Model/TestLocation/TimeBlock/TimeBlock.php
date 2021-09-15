<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\TimeBlock;

use DateTimeInterface;

class TimeBlock implements TimeBlockInterface
{
    /**
     * @see \LML\SDK\Repository\TestLocationRepository::getTimeBlocks
     */
    public function __construct(
        protected string            $id,
        protected DateTimeInterface $startsAt,
        protected DateTimeInterface $endsAt,
        protected ?string           $description,
    )
    {
    }

    public function setStartsAt(DateTimeInterface $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function setEndsAt(DateTimeInterface $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getStartsAt(): DateTimeInterface
    {
        return $this->startsAt;
    }

    public function getEndsAt(): DateTimeInterface
    {
        return $this->endsAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray()
    {
        return [
            'id'          => $this->getId(),
            'starts_at'   => $this->getStartsAt()->format('Y-m-d'),
            'ends_at'     => $this->getEndsAt()->format('Y-m-d'),
            'description' => $this->getDescription(),
        ];
    }
}
