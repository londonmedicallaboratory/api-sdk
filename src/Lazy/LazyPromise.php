<?php

declare(strict_types=1);

namespace LML\SDK\Lazy;

use Traversable;
use IteratorAggregate;
use React\Promise\PromiseInterface;
use LML\View\Lazy\LazyValueInterface;
use function Clue\React\Block\await;

/**
 * @template T
 *
 * @implements LazyValueInterface<T>
 * @implements IteratorAggregate<T>
 */
class LazyPromise implements LazyValueInterface, IteratorAggregate
{


    private bool $evaluated = false;

    /**
     * @todo Fix mixed typehint once stubs are added into react package
     *
     * @param PromiseInterface<T> $promise
     */
    public function __construct(private PromiseInterface $promise)
    {
        $this->promise->then(function ($data): mixed {
            $this->evaluated = true;

            return $data;
        });
    }

    public function isEvaluated(): bool
    {
        return $this->evaluated;
    }

    public function getValue()
    {
        return await($this->promise);
    }

    public function getIterator(): Traversable
    {
        yield from $this->getValue();
    }
}
