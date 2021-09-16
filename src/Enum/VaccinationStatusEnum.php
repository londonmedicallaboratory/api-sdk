<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

class VaccinationStatusEnum extends AbstractEnum
{
    public const VACCINATED = 'vaccinated';
    public const NOT_VACCINATED = 'not_vaccinated';

    protected static function getDefinitions(): iterable
    {
        yield [self::VACCINATED, 'Vaccinated'];
        yield [self::NOT_VACCINATED, 'Not vaccinated'];
    }
}
