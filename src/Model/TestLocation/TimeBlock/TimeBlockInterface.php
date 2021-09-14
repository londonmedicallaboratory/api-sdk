<?php

declare(strict_types=1);

namespace LML\SDK\Model\TestLocation\TimeBlock;

use DateTimeInterface;
use LML\SDK\Model\ModelInterface;

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

    public function getEndsAt(): DateTimeInterface;

    public function getDescription(): ?string;
}
