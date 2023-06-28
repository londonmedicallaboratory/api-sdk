<?php

declare(strict_types=1);

namespace LML\SDK\Component;

use Closure;
use Generator;
use SplObjectStorage;
use IteratorAggregate;
use UnexpectedValueException;
use function array_merge;

/**
 * @template T of object
 * @template R of object
 *
 * @implements IteratorAggregate<array-key, R>
 */
class GroupedStorage implements IteratorAggregate
{
    /**
     * @var SplObjectStorage<R, list<T>>
     */
    private SplObjectStorage $storage;

    /**
     * @param array<T> $data
     * @param Closure(T): R $groupBy
     */
    public function __construct(
        private array $data,
        private Closure $groupBy,
    )
    {
        $storage = new SplObjectStorage();
        $this->storage = $storage;
        $this->resolve();
    }

    /**
     * @return list<R>
     */
    public function getGroups(): array
    {
        $storage = $this->storage;
        $groups = [];
        foreach ($storage as $value) {
            $groups[] = $value;
        }

        return $groups;
    }

    /**
     * @param R $parent
     *
     * @return list<T>
     */
    public function getChildrenOf(object $parent): array
    {
        $storage = $this->storage;
        try {
            return $storage[$parent];
        } catch (UnexpectedValueException) {
            return [];
        }
    }

    public function getIterator(): Generator
    {
        yield from $this->getGroups();
    }

    private function resolve(): void
    {
        $storage = $this->storage;
        $callback = $this->groupBy;

        foreach ($this->data as $datum) {
            $parent = $callback($datum);
            if (!isset($storage[$parent])) {
                $storage->attach($parent, []);
            }
            $previous = $storage[$parent];
            $storage[$parent] = array_merge($previous, [$datum]);
        }
    }
}