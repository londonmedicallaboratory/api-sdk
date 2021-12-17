<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

enum TransportTypeEnum: string implements NameableInterface
{
    case PLANE = 'plane';
    case TRAIN = 'train';
    case VESSEL = 'vessel';

    public function getName(): string
    {
        return match ($this) {
            self::PLANE => 'Plane',
            self::TRAIN => 'Train',
            self::VESSEL => 'Vessel',
        };
    }
}
