<?php

declare(strict_types=1);

namespace LML\SDK\Service\IdentityMap;

use Closure;
use RuntimeException;
use Symfony\Contracts\Service\ResetInterface;
use function sprintf;
use function get_debug_type;

class IdentityMap implements ResetInterface
{
    /**
     * @var array<class-string, array<string, object>>
     */
    private array $map = [];

    public function reset(): void
    {
        $this->map = [];
    }

    /**
     * @template C of object
     *
     * @param class-string<C> $className
     * @param Closure(): C $builder
     *
     * @return C
     */
    public function get(string $className, string $id, Closure $builder)
    {
        $map = $this->map;
        $model = $map[$className][$id] ?? $this->doGet($className, $id, $builder);
        // make psalm happy; eventually refactor $map to work with objects, not arrays, to have correct type signature
        if (!$model instanceof $className) {
            throw new RuntimeException('This should never happen.');
        }

        return $model;
    }

    /**
     * @template B of object
     *
     * @param class-string<B> $className
     * @param Closure(): B $build
     *
     * @return B
     */
    private function doGet(string $className, string $id, Closure $builder)
    {
        $model = $builder();
        if (!$model instanceof $className) {
            throw new RuntimeException(sprintf('Builder should return instance of "%s", got "%s".', $className, get_debug_type($model)));
        }
        $this->map[$className][$id] = $model;

        return $model;
    }
}
