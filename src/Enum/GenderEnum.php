<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

class GenderEnum extends AbstractEnum
{
    public const MALE = 'male';
    public const FEMALE = 'female';
    public const EITHER = 'either';

    protected static function getDefinitions(): iterable
    {
        yield [self::MALE, 'Male'];
        yield [self::FEMALE, 'Female'];
        yield [self::EITHER, 'Prefer not to answer'];
    }
}
