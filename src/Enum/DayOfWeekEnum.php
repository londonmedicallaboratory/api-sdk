<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use InvalidArgumentException;
use LML\SDK\Enum\Model\NameableInterface;

enum DayOfWeekEnum: int implements NameableInterface
{
    case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

    public function getName(): string
    {
        return match ($this) {
            self::SUNDAY => 'Sunday',
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
        };
    }

    public function getShortcut(): string
    {
        return match ($this) {
            self::SUNDAY => 'sun',
            self::MONDAY => 'mon',
            self::TUESDAY => 'tue',
            self::WEDNESDAY => 'wed',
            self::THURSDAY => 'thu',
            self::FRIDAY => 'fri',
            self::SATURDAY => 'sat',
        };
    }

    public static function fromShortcut(string $short): self
    {
        return match ($short) {
            'sun' => self::SUNDAY,
            'mon' => self::MONDAY,
            'tue' => self::TUESDAY,
            'wed' => self::WEDNESDAY,
            'thu' => self::THURSDAY,
            'fri' => self::FRIDAY,
            'sat' => self::SATURDAY,
            default => throw new InvalidArgumentException(sprintf('Day \'%s\' is not valid.', $short)),
        };
    }
}
