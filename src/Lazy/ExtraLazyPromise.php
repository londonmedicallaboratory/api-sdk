<?php

declare(strict_types=1);

namespace LML\SDK\Lazy;

use Closure;
use LogicException;
use LML\View\Lazy\Store;
use React\Promise\PromiseInterface;
use LML\View\Lazy\LazyValueInterface;
use function Clue\React\Block\await;

/**
 * @todo YOU MUST FIX THESE SUPPRESSIONS
 *
 * @template T
 *
 * @implements LazyValueInterface<T>
 * @implements PromiseInterface<T>
 *
 * @psalm-suppress InternalClass
 * @psalm-suppress InternalMethod
 * @psalm-suppress MethodSignatureMismatch
 */
class ExtraLazyPromise implements LazyValueInterface, PromiseInterface
{
    /**
     * @var null|Store<T>
     */
    private ?Store $store = null;

    private bool $evaluated = false;

    /**
     * @var null|PromiseInterface<T>
     */
    private ?PromiseInterface $promise = null;

    /**
     * @param Closure(): PromiseInterface<T> $callable
     */
    public function __construct(private Closure $callable)
    {
    }

    public function __toString(): string
    {
        return (string)$this->getValue();
    }

    /**
     * @return PromiseInterface<T>
     */
    public function getPromise(): PromiseInterface
    {
        return $this->promise ??= $this->doGetPromise();
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null): PromiseInterface
    {
        return $this->getPromise()->then($onFulfilled, $onRejected, $onProgress);
    }

    public function otherwise($onRejected): never
    {
        throw new LogicException('Not supported.');
    }

    public function isEvaluated(): bool
    {
        return $this->evaluated;
    }

    /**
     * @return T
     */
    public function getValue()
    {
        $store = $this->store ??= $this->doGetStore();

        return $store->getValue();
    }

    /**
     * @return Store<T>
     */
    private function doGetStore(): Store
    {
        $promise = $this->getPromise();

        return new Store(await($promise));

//        return $this->store;
    }

    /**
     * @return PromiseInterface<T>
     */
    private function doGetPromise(): PromiseInterface
    {
        $callable = $this->callable;
        $promise = $callable();
        $promise->then(function ($data): mixed {
            $this->evaluated = true;

            return $data;
        });

        return $promise;
    }
}
