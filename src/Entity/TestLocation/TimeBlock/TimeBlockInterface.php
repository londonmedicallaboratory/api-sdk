<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\TimeBlock;

use DateTimeInterface;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      starts_at: string,
 *      ends_at: string,
 *      description: ?string,
 * }
 *
 * @extends ModelInterface<S>
 */
interface TimeBlockInterface extends ModelInterface
{
    public function getStartsAt(): DateTimeInterface;

    public function setStartsAt(DateTimeInterface $startsAt): void;

    public function getEndsAt(): DateTimeInterface;

    public function setEndsAt(DateTimeInterface $endsAt): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;
}
