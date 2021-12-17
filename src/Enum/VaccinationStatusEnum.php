<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

enum VaccinationStatusEnum: string implements NameableInterface
{
    case VACCINATED = 'vaccinated';
    case NOT_VACCINATED = 'not_vaccinated';

    public function getName(): string
    {
        return match ($this) {
            self::VACCINATED => 'Vaccinated',
            self::NOT_VACCINATED => 'Not vaccinated',
        };
    }
}
