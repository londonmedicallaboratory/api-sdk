<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Brand\TimeBlock;

use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Repository\BrandRepository;

/**
 * @see BrandRepository::getTimeBlocks
 *
 * @psalm-type S=array{
 *      id: string,
 *      starts_at: string,
 *      ends_at: string,
 *      description: ?string,
 * }
 *
 * @implements ModelInterface<S>
 */
class TimeBlock implements ModelInterface
{
    public function __construct(
        protected string $id,
        protected DateTimeInterface $startsAt,
        protected DateTimeInterface $endsAt,
        protected ?string $description,
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
            'id' => $this->getId(),
            'starts_at' => $this->getStartsAt()->format('Y-m-d'),
            'ends_at' => $this->getEndsAt()->format('Y-m-d'),
            'description' => $this->getDescription(),
        ];
    }
}
