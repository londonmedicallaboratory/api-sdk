<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

enum GenderEnum: string implements NameableInterface
{
    case MALE = 'male';
    case FEMALE = 'female';
    case NON_BINARY = 'non_binary';
    case PREFER_NOT_TO_SAY = 'prefer_not_to_say';

    public function getName(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::NON_BINARY => 'Non binary',
            self::PREFER_NOT_TO_SAY => 'Prefer not to say',
        };
    }
}
