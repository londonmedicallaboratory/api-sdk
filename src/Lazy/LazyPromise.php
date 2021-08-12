<?php

declare(strict_types=1);

namespace LML\SDK\Lazy;

use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use LML\View\Lazy\LazyValueInterface;
use function Clue\React\Block\await;

/**
 * @template T
 *
 * @implements LazyValueInterface<T>
 */
class LazyPromise implements LazyValueInterface
{
    /**
     * @param PromiseInterface<T> $promise
     */
    public function __construct(private PromiseInterface $promise)
    {
    }

    public function getValue()
    {
        return await($this->promise, Loop::get());
    }
}
