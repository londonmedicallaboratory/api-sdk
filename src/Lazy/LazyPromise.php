<?php

declare(strict_types=1);

namespace LML\SDK\Lazy;

use Traversable;
use IteratorAggregate;
use React\Promise\PromiseInterface;
use LML\View\Lazy\LazyValueInterface;
use React\Promise\Internal\FulfilledPromise;
use LML\SDK\Exception\DataNotFoundException;
use function React\Async\await;

/**
 * @template T of mixed
 *
 * @implements LazyValueInterface<T>
 * @implements IteratorAggregate<T>
 */
class LazyPromise implements LazyValueInterface, IteratorAggregate
{
    private bool $evaluated = false;

    /**
     * @param PromiseInterface<T> $promise
     */
    public function __construct(private PromiseInterface $promise)
    {
        /** @psalm-suppress InvalidArgument */
        $this->promise->then(onFulfilled: $this->whenComplete(...), onRejected: fn() => throw new DataNotFoundException());
    }

    public function isEvaluated(): bool
    {
        return $this->promise instanceof FulfilledPromise || $this->evaluated;
    }

    public function getValue()
    {
        return await($this->promise);
    }

    public function getIterator(): Traversable
    {
        yield from $this->getValue();
    }

    /**
     * @param T $data
     *
     * @return T
     */
    private function whenComplete(mixed $data): mixed
    {
        $this->evaluated = true;

        return $data;
    }
}
