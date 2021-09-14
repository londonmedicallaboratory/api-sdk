<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

class DayOfWeekEnum extends AbstractEnum
{
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    protected static function getDefinitions(): iterable
    {
        yield [self::SUNDAY, 'Sunday'];
        yield [self::MONDAY, 'Monday'];
        yield [self::TUESDAY, 'Tuesday'];
        yield [self::WEDNESDAY, 'Wednesday'];
        yield [self::THURSDAY, 'Thursday'];
        yield [self::FRIDAY, 'Friday'];
        yield [self::SATURDAY, 'Saturday'];
    }
}
