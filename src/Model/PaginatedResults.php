<?php

declare(strict_types=1);

namespace LML\SDK\Model;

use Traversable;
use IteratorAggregate;

/**
 * @template T
 * @implements IteratorAggregate<T>
 */
class PaginatedResults implements IteratorAggregate
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        protected int   $currentPage,
        protected int   $nrOfPages,
        protected int   $resultsPerPage,
        protected ?int  $nextPage,
        protected array $items,
    )
    {
    }

    /**
     * @return ?T
     */
    public function first()
    {
        $first = reset($this->items);

        return $first ?: null;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getNrOfPages(): int
    {
        return $this->nrOfPages;
    }

    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @return list<T>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getIterator(): Traversable
    {
        yield from $this->getItems();
    }
}
