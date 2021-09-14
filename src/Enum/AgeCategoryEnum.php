<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

class AgeCategoryEnum extends AbstractEnum
{
    public const YEARS = 'years';
    public const MONTHS = 'months';

    protected static function getDefinitions(): iterable
    {
        yield [self::YEARS, 'Years'];
        yield [self::MONTHS, 'Months'];
    }
}
