<?php

declare(strict_types=1);

namespace LML\SDK\Entity;

use Traversable;
use IteratorAggregate;

/**
 * @template-covariant T
 * @implements IteratorAggregate<T>
 */
class PaginatedResults implements IteratorAggregate
{
    /**
     * @param list<T> $items
     * @param int<0, max> $nrOfPages
     * @param int<0, max> $resultsPerPage
     */
    public function __construct(
        protected int   $currentPage,
        protected int   $nrOfPages,
        protected int   $resultsPerPage,
        protected ?int  $nextPage,
        protected int   $nrOfResults,
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

    /**
     * @return int<0, max>
     */
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

    public function getNrOfResults(): int
    {
        return $this->nrOfResults;
    }
}
