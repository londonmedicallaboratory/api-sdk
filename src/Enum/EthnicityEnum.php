<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use RuntimeException;
use LML\SDK\Enum\Model\NameableInterface;
use function sprintf;
use function str_starts_with;

enum EthnicityEnum: string implements NameableInterface
{
    // list all possible ethnicities here
    case WHITE_ENGLISH = 'white_english';
    case WHITE_IRISH = 'white_irish';
    case WHITE_GYPSY = 'white_gypsy';
    case WHITE_OTHER = 'white_other';

    case MIXED_WHITE_AND_BLACK_CARIBBEAN = 'mixed_white_and_black_caribbean';
    case MIXED_WHITE_AND_BLACK_AFRICAN = 'mixed_white_and_black_african';
    case MIXED_WHITE_AND_ASIAN = 'mixed_white_and_asian';
    case MIXED_OTHER = 'mixed_other';

    case ASIAN_INDIAN = 'asian_indian';
    case ASIAN_PAKISTANI = 'asian_pakistani';
    case ASIAN_BANGLADESHI = 'asian_bangladeshi';
    case ASIAN_CHINESE = 'asian_chinese';
    case ASIAN_OTHER = 'asian_other';

    case BLACK_AFRICAN = 'black_african';
    case BLACK_CARIBBEAN = 'black_caribbean';
    case BLACK_OTHER = 'black_other';

    case OTHER_ARAB = 'other_arab';
    case OTHER_UNDEFINED = 'other_undefined';

    public function getName(): string
    {
        return match ($this) {
            self::WHITE_ENGLISH => 'English / Welsh / Scottish / Northern Irish / British',
            self::WHITE_IRISH => 'Irish',
            self::WHITE_GYPSY => 'Gypsy or Irish Traveller',
            self::WHITE_OTHER => 'Any other White background',

            self::MIXED_WHITE_AND_BLACK_CARIBBEAN => 'White and Black Caribbean',
            self::MIXED_WHITE_AND_BLACK_AFRICAN => 'White and Black African',
            self::MIXED_WHITE_AND_ASIAN => 'White and Asian',
            self::MIXED_OTHER => 'Any other Mixed / Multiple ethnic background',

            self::ASIAN_INDIAN => 'Indian',
            self::ASIAN_PAKISTANI => 'Pakistani',
            self::ASIAN_BANGLADESHI => 'Bangladeshi',
            self::ASIAN_CHINESE => 'Chinese',
            self::ASIAN_OTHER => 'Any other Asian background',

            self::BLACK_AFRICAN => 'African',
            self::BLACK_CARIBBEAN => 'Caribbean',
            self::BLACK_OTHER => 'Any other Black / African / Caribbean background',

            self::OTHER_ARAB => 'Arab',
            self::OTHER_UNDEFINED => 'Any other ethnic group',
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function getAsFormGroupChoices(): array
    {
        $groups = [];
        foreach (self::cases() as $enum) {
            $groupName = self::getGroupName($enum->name);
            $groups[$groupName][$enum->value] = $enum;
        }

        return $groups;
    }

    private static function getGroupName(string $enum): string
    {
        $enum = strtolower($enum);

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
