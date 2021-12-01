<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use ReflectionClass;
use InvalidArgumentException;
use LML\SDK\Exception\InvalidEnumException;
use function sprintf;
use function implode;
use function array_slice;
use function str_replace;

/**
 * @todo Remove in PHP 8.1
 */
abstract class AbstractEnum
{
    /**
     * @return array<string, string|int>
     */
    public static function getAsFormChoices(): array
    {
        $choices = [];
        foreach (static::getDefinitions() as [$const, $label]) {
            $choices[$label] = $const;
        }

        return $choices;
    }

    /**
     * @psalm-assert static::* $name
     */
    public static function assertValidEnum(?string $name, bool $allowNull = false, ?string $message = null): void
    {
        if (null === $name) {
            if ($allowNull) {
                return;
            }
            throw new InvalidEnumException('Value not provided.');
        }

        foreach (static::getDefinitions() as [$const]) {
            if ($const === $name) {
                return;
            }
        }

        // if message is not provided, create one; use class name and remove `Enum` word from it
        if (!$message) {
            $rc = new ReflectionClass(static::class);
            $shortName = $rc->getShortName();
            $shortName = str_replace('Enum', '', $shortName);

            $acceptedValues = implode(', ', self::getAsFormChoices());
            $message = sprintf('%s \'%s\' is not allowed. Allowed values are: %s', $shortName, $name, $acceptedValues);
        }

        throw new InvalidEnumException($message);
    }

    /**
     * @param string $name
     *
     * @return array<string|int>
     */
    public static function getExtraInfo(string $name): array
    {
        foreach (static::getDefinitions() as $definition) {
            [$const] = $definition;
            if ($const === $name) {
                return array_slice($definition, 2);
            }
        }

        throw new InvalidArgumentException(sprintf('Method "%s" is not found in list of definitions.', $name));
    }

    public static function getViewFormat(string|int $method, ?string $default = null): string
    {
        foreach (static::getDefinitions() as [$const, $label]) {
            if ($const === $method) {
                return $label;
            }
        }

        // allow user to show '--unknown--' or similar message instead of Exception
        if (null !== $default) {
            return $default;
        }

        throw new InvalidArgumentException(sprintf('Method "%s" is not found in list of definitions.', $method));
    }

    /**
     * @return iterable<array-key, array{0: string|int, 1: string}>
     */
    abstract protected static function getDefinitions(): iterable;
}
