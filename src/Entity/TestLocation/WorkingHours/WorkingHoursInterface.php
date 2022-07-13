<?php

declare(strict_types=1);

namespace LML\SDK\Entity\TestLocation\WorkingHours;

use LML\SDK\Enum\DayOfWeekEnum;
use LML\SDK\Entity\ModelInterface;

/**
 * @psalm-type S=array{
 *     id: string,
 *     day_of_week: string,
 *     starts_at: string,
 *     ends_at: string,
 *     active?: bool,
 * }
 *
 * @extends ModelInterface<S>
 */
interface WorkingHoursInterface extends ModelInterface
{
    public function getDayOfWeek(): DayOfWeekEnum;

    public function getStartsAt(): string;

    public function getEndsAt(): string;

    public function isActive(): bool;
}
