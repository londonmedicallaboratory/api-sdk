<?php

declare(strict_types=1);

namespace LML\SDK\Pager;

use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\PaginatedResults;
use Pagerfanta\Adapter\AdapterInterface;
use function Clue\React\Block\await;

/**
 * @template T
 * @template TPaginated of PaginatedResults<T>
 *
 * @implements AdapterInterface<T>
 */
class PromiseAdapter implements AdapterInterface
{
    /**
     * @var null|TPaginated
     */
    private ?PaginatedResults $cache = null;

    /**
     * @param PromiseInterface<TPaginated> $promise
     */
    public function __construct(
        private PromiseInterface $promise,
    )
    {
    }

    public function getNbResults(): int
    {
        return $this->getPaginatedResults()->getNrOfPages();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        return $this->getPaginatedResults()->getItems();
    }

    /**
     * @return TPaginated
     */
    private function getPaginatedResults(): PaginatedResults
    {
        return $this->cache ??= $this->doGetPaginatedResults();
    }

    /**
     * @return TPaginated
     */
    private function doGetPaginatedResults(): PaginatedResults
    {
        return await($this->promise, Loop::get());
    }
}
