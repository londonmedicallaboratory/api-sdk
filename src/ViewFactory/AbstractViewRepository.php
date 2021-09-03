<?php

declare(strict_types=1);

namespace LML\SDK\ViewFactory;

use RuntimeException;
use React\EventLoop\Loop;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Model\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Model\PaginatedResults;
use LML\View\Lazy\LazyValueInterface;
use Psr\Http\Message\ResponseInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Exception\DataNotFoundException;
use LML\View\ViewFactory\AbstractViewFactory;
use function rtrim;
use function sprintf;
use function Clue\React\Block\await;

/**
 * @template TData
 * @template TView of ModelInterface
 * @template TFilters of array
 *
 * @extends AbstractViewFactory<TData, TView, array, array>
 *
 * @see ModelInterface
 */
abstract class AbstractViewRepository extends AbstractViewFactory
{
    /**
     * @var array<string, TView>
     */
    private array $cache = [];

    private ?ClientInterface $client = null;

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     *
     * @psalm-suppress MixedArrayAccess
     */
    public function find(string $id, bool $await = false)
    {
        $url = sprintf('%s/%s', $this->getBaseUrl(), $id);
        $client = $this->getClient();

        $promise = $client->getAsync($url)
            ->then(function ($data) {
                if (!$data) {
                    return null;
                }
                $id = (string)$data['id'];

                /** @psalm-suppress MixedArgument */
                return $this->cache[$id] ??= $this->buildOne($data);
            });

        return $await ? await($promise, Loop::get()) : $promise;
    }

    public function patchId(string $id, array $data): PromiseInterface
    {
        $client = $this->getClient();

        return $client->patch($this->getBaseUrl() . '/' . $id, $data);
    }

    /**
     * @param TView $model
     *
     * @return PromiseInterface<ResponseInterface>
     */
    public function persist($model)
    {
        $client = $this->getClient();

        return $client->post($this->getBaseUrl(), $model->toArray());
    }

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
     * Finding one by slug is mostly used query so this method is just a shortcut.
     *
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function findOneBySlug(string $slug, bool $await = false)
    {
        $promise = $this->findOneBy(['slug' => $slug]);

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function findOneBy(array $filters = [], ?string $url = null, bool $await = false)
    {
        $paginated = $this->findPaginated($filters, $url);

        $promise = $paginated->then(function (PaginatedResults $results) {
            return $results->first();
        });

        return $await ? await($promise, Loop::get()) : $promise;
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
        $client = $this->getClient();
        if (!$url) {
            $url = rtrim($this->getBaseUrl(), '/') . '/'; // Symfony trailing slash issue; this will avoid 301 redirections
        }

        /** @var PromiseInterface<array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<TData>}> $promise */
        $promise = $client->getAsync($url, $filters, $page);

        return $promise
            ->then(function (array $data) {
                $views = [];
                $items = $data['items'];
                foreach ($items as $item) {
                    /** @var ?string $id */
                    $id = $item['id'] ?? throw new RuntimeException();
                    /** @psalm-suppress PossiblyInvalidArgument */
                    $view = $this->cache[(string)$id] ??= $this->buildOne($item);
                    $views[] = $view;
                }

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
     * @return PaginatedResults<TView>
     */
    public function awaitPaginated(array $filters = [], ?string $url = null, int $page = 1)
    {
        $promise = $this->findPaginated($filters, $url, $page);
        $lazy = new LazyPromise($promise);

        return $lazy->getValue();
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

    protected function getClient(): ClientInterface
    {
        return $this->client ?? throw new RuntimeException('Client is not defined.');
    }

    abstract protected function getBaseUrl(): string;
}
