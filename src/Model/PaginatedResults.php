<?php

declare(strict_types=1);

namespace LML\SDK\Model;

/**
 * @template T
 */
class PaginatedResults
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        private int $currentPage,
        private int $nrOfPages,
        private int $resultsPerPage,
        private ?int $nextPage,
        private $items,
    )
    {
    }

    /**
     * @return ?T
     */
    public function first()
    {
        return reset($this->items);
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
    public function getItems()
    {
        return $this->items;
    }
}
