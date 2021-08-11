<?php

declare(strict_types=1);

namespace LML\SDK\ViewFactory;

use RuntimeException;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Service\Client\Client;
use React\Promise\PromiseInterface;
use LML\SDK\Model\PaginatedResults;
use LML\View\Lazy\LazyValueInterface;
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
    private ?Client $client = null;

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
     *
     * @return PromiseInterface<list<TView>>
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function findBy(array $filters = [], ?string $url = null, int $page = 1, &$stored = []): PromiseInterface
    {
        return $this->findPaginated($filters, $url, $page)
            ->then(
                /** @param PaginatedResults<TView> $pagerfanta */
                function (PaginatedResults $pagerfanta) use ($filters, $url, &$stored) {

                foreach ($pagerfanta->getItems() as $item) {
                    $stored[] = $item;
                }
                $nextPage = $pagerfanta->getNextPage();
                if (!$nextPage) {
                    return $stored;
                }

                return $this->findBy($filters, $url, $nextPage, $stored);
            });
    }

    /**
     * @return PromiseInterface<PaginatedResults<TView>>
     *
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function findPaginated(array $filters = [], ?string $baseUrl = null, int $page = 1): PromiseInterface
    {
        $client = $this->client ?? throw new RuntimeException();

        return $client->getAsyncPromise($baseUrl ?? $this->getBaseUrl(), $filters, $page)
            ->then(
                /** @param array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<TData>} $data */
                function (array $data) {
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

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    abstract protected function getBaseUrl(): string;
}
