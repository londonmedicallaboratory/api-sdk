<?php

declare(strict_types=1);

namespace LML\SDK\ViewFactory;

use RuntimeException;
use LML\SDK\Lazy\LazyPromise;
use React\Promise\PromiseInterface;
use LML\SDK\Model\PaginatedResults;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Exception\DataNotFoundException;
use LML\View\ViewFactory\AbstractViewFactory;

/**
 * @template TData
 * @template TView
 * @template TFilters of array
 *
 * @extends AbstractViewFactory<TData, TView, array, array>
 */
abstract class AbstractViewRepository extends AbstractViewFactory
{
    private ?ClientInterface $client = null;

    /**
     * @param TFilters $filters
     *
     * @return LazyValueInterface<?TView>
     */
    public function findLazy(array $filters, ?string $url = null)
    {
        $promise = $this->findOneBy($filters, $url);

        return new LazyPromise($promise);
    }

    /**
     * @return LazyValueInterface<?TView>
     */
    public function findLazyBySlug(string $slug)
    {
        $promise = $this->findOneBy(['slug' => $slug]);

        return new LazyPromise($promise);
    }

    /**
     * @return PromiseInterface<?TView>
     */
    public function findOneBy(array $filters = [], ?string $url = null)
    {
        $paginated = $this->findPaginated($filters, $url);

        return $paginated->then(function (PaginatedResults $results) {
            return $results->first();
        });
    }

    /**
     * @return PromiseInterface<TView>
     */
    public function findOneByOrException(array $filters = [], ?string $url = null)
    {
        $paginated = $this->findPaginated($filters, $url);

        return $paginated->then(function (PaginatedResults $results) {
            return $results->first() ?? throw new DataNotFoundException();
        });
    }

    /**
     * @param list<TView> $stored
     * @param-in list<TView> $stored
     * @param-out list<TView> $stored
     *
     * @return PromiseInterface<list<TView>>
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArgument
     *
     * @todo Fix this mess
     */
    public function findBy(array $filters = [], ?string $url = null, int $page = 1, &$stored = []): PromiseInterface
    {
        $promise = $this->findPaginated($filters, $url, $page);

        return $promise->then(function (PaginatedResults $paginatedResults) use ($filters, $url, &$stored) {
            foreach ($paginatedResults->getItems() as $item) {
                $stored[] = $item;
            }
            $nextPage = $paginatedResults->getNextPage();
            if (!$nextPage) {
                return $stored;
            }

            return $this->findBy($filters, $url, $nextPage, $stored);
        });
    }

    /**
     * @return PromiseInterface<PaginatedResults<TView>>
     */
    public function findPaginated(array $filters = [], ?string $url = null, int $page = 1): PromiseInterface
    {
        $client = $this->client ?? throw new RuntimeException();

        /** @var PromiseInterface<array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<TData>}> $promise */
        $promise = $client->getAsync($url ?? $this->getBaseUrl(), $filters, $page);

        return $promise
            ->then(function (array $data) {
                $views = $this->build($data['items']);

                return new PaginatedResults(
                    currentPage: $data['current_page'],
                    nrOfPages: $data['nr_of_pages'],
                    resultsPerPage: $data['results_per_page'],
                    nextPage: $data['next_page'],
                    items: $views,
                );
            });
    }

    /**
     * @param TFilters $filters
     */
    public function findFromUrl(string $url, array $filters = []): PromiseInterface
    {
        return $this->findBy(filters: $filters, url: $url);
    }

    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    abstract protected function getBaseUrl(): string;
}
