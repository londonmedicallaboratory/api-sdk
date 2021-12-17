<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

enum GenderEnum: string implements NameableInterface
{
    case MALE = 'male';
    case FEMALE = 'female';
    case EITHER = 'either';

    public function getName(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::EITHER => 'Prefer not to answer',
        };
    }
}
