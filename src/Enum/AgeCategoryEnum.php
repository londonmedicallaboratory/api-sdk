<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

enum AgeCategoryEnum: string implements NameableInterface
{
    case YEARS = 'years';
    case MONTHS = 'months';

    public function getName(): string
    {
        return match ($this) {
            self::YEARS => 'Years',
            self::MONTHS => 'Months',
        };
    }
}
