<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use RuntimeException;
use function sprintf;
use function str_starts_with;

class EthnicityEnum extends AbstractEnum
{
    // list all possible ethnicities here
    public const WHITE_ENGLISH = 'white_english';
    public const WHITE_IRISH = 'white_irish';
    public const WHITE_GYPSY = 'white_gypsy';
    public const WHITE_OTHER = 'white_other';

    public const MIXED_WHITE_AND_BLACK_CARIBBEAN = 'mixed_white_and_black_caribbean';
    public const MIXED_WHITE_AND_BLACK_AFRICAN = 'mixed_white_and_black_african';
    public const MIXED_WHITE_AND_ASIAN = 'mixed_white_and_asian';
    public const MIXED_OTHER = 'mixed_other';

    public const ASIAN_INDIAN = 'asian_indian';
    public const ASIAN_PAKISTANI = 'asian_pakistani';
    public const ASIAN_BANGLADESHI = 'asian_bangladeshi';
    public const ASIAN_CHINESE = 'asian_chinese';
    public const ASIAN_OTHER = 'asian_other';

    public const BLACK_AFRICAN = 'black_african';
    public const BLACK_CARIBBEAN = 'black_caribbean';
    public const BLACK_OTHER = 'black_other';

    public const OTHER_ARAB = 'other_arab';
    public const OTHER_UNDEFINED = 'other_undefined';

    /**
     * @return array<string, array<string, int|string>>
     */
    public static function getAsFormGroupChoices()
    {
        $groups = [];
        foreach (self::getDefinitions() as [$const, $label]) {
            $groupName = self::getGroupName($const);
            $groups[$groupName][$label] = $const;
        }

        return $groups;
    }

    protected static function getDefinitions(): iterable
    {
        yield [self::WHITE_ENGLISH, 'English / Welsh / Scottish / Northern Irish / British'];
        yield [self::WHITE_IRISH, 'Irish'];
        yield [self::WHITE_GYPSY, 'Gypsy or Irish Traveller'];
        yield [self::WHITE_OTHER, 'Any other White background'];

        yield [self::MIXED_WHITE_AND_BLACK_CARIBBEAN, 'White and Black Caribbean'];
        yield [self::MIXED_WHITE_AND_BLACK_AFRICAN, 'White and Black African'];
        yield [self::MIXED_WHITE_AND_ASIAN, 'White and Asian'];
        yield [self::MIXED_OTHER, 'Any other Mixed / Multiple ethnic background'];

        yield [self::ASIAN_INDIAN, 'Indian'];
        yield [self::ASIAN_PAKISTANI, 'Pakistani'];
        yield [self::ASIAN_BANGLADESHI, 'Bangladeshi'];
        yield [self::ASIAN_CHINESE, 'Chinese'];
        yield [self::ASIAN_OTHER, 'Any other Asian background'];

        yield [self::BLACK_AFRICAN, 'African'];
        yield [self::BLACK_CARIBBEAN, 'Caribbean'];
        yield [self::BLACK_OTHER, 'Any other Black / African / Caribbean background'];

        yield [self::OTHER_ARAB, 'Arab'];
        yield [self::OTHER_UNDEFINED, 'Any other ethnic group'];
    }

    private static function getGroupName(int|string $enum): string
    {
        $enum = (string)$enum;

        return match (true) {
            str_starts_with($enum, 'white_') => 'White',
            str_starts_with($enum, 'mixed_') => 'Mixed / Multiple ethnic groups',
            str_starts_with($enum, 'asian_') => 'Asian / Asian British',
            str_starts_with($enum, 'black_') => 'Black / African / Caribbean / Black British',
            str_starts_with($enum, 'other_') => 'Other ethnic group',
            default => throw new RuntimeException(sprintf('Enum "%s" is not supported.', $enum)),
        };
    }
}
