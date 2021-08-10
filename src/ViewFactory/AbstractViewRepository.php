<?php

declare(strict_types=1);

namespace LML\SDK\ViewFactory;

use RuntimeException;
use Pagerfanta\Pagerfanta;
use LML\SDK\Service\Client\Client;
use LML\SDK\Model\PaginatedResults;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use LML\View\ViewFactory\AbstractViewFactory;
use function sprintf;

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

//    public function findAsyncPaginated(array $filters = [], ?string $baseUrl = null, int $page = 1)
//    {
//        $client = $this->client ?? throw new RuntimeException();
//
//    }

    /**
     * @param TFilters $filters
     *
     * @return Pagerfanta<TView>
     */
    public function findPaginated(array $filters = [], ?string $baseUrl = null, int $page = 1)
    {
        $client = $this->client ?? throw new RuntimeException();

        /** @var array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<TData>} $data */
        $data = $client->get($baseUrl ?? $this->getBaseUrl(), $filters, $page);

        $views = $this->build($data['items']);

        $adapter = new CallbackAdapter(fn() => $data['nr_of_results'], fn() => $views);

        return new Pagerfanta($adapter);
    }

    /**
     * @param TFilters $filters
     *
     * @return ?TView
     */
    public function findOneBy(array $filters = [], ?string $url = null)
    {
        $paginated = $this->findPaginated($filters, $url);
        $items = $paginated->getCurrentPageResults();
        foreach ($items as $item) {
            return $item;
        }

        return null;
    }

    /**
     * @param TFilters $filters
     *
     * @return list<TView>
     */
    public function findBy(array $filters = [], ?string $url = null)
    {
        $page = 1;
        $results = [];
        do {
            $paginated = $this->findPaginated($filters, $url, $page);
            foreach ($paginated->getCurrentPageResults() as $item) {
                $results[] = $item;
            }
            $page++;
        } while ($paginated->hasNextPage());

        return $results;
    }

    /**
     * @param TFilters $filters
     *
     * @return list<TView>
     */
    public function findFromUrl(string $url, array $filters = [])
    {
        return $this->findBy(filters: $filters, url: $url);
    }

    /**
     * @param TFilters $filters
     *
     * @return ?TView
     */
    public function findOneFromUrl(string $url, array $filters = [])
    {
        return $this->findOneBy(filters: $filters, url: $url);
    }

    /**
     * @return ?TView
     */
    public function find(string $id)
    {
        $url = sprintf('%s/%s', $this->getBaseUrl(), $id);

        return $this->findOneBy(url: $url);
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    abstract protected function getBaseUrl(): string;
}
