<?php

declare(strict_types=1);

namespace LML\SDK\Util;

use ReflectionClass;
use ReflectionAttribute;

class ReflectionAttributeReader
{
    /**
     * @template T of object
     *
     * @param class-string $className
     * @param class-string<T> $expected
     *
     * @return ?T
     */
    public static function getAttribute(string $className, string $expected): ?object
    {
        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes($expected, ReflectionAttribute::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first?->newInstance();
    }
}
