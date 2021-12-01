<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

class TransportTypeEnum extends AbstractEnum
{
    public const PLANE = 'plane';
    public const TRAIN = 'train';
    public const VESSEL = 'vessel';

    protected static function getDefinitions(): iterable
    {
        yield [self::PLANE, 'Plane'];
        yield [self::TRAIN, 'Train'];
        yield [self::VESSEL, 'Vessel'];
    }
}
