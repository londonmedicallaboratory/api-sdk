<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use Throwable;
use LogicException;
use ReflectionClass;
use LML\SDK\Attribute\Entity;
use LML\SDK\Event\PostFlushEvent;
use LML\SDK\Event\PreUpdateEvent;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Event\PrePersistEvent;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Exception\FlushException;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\ResponseException;
use LML\SDK\Event\PreFlushNewEntitiesEvent;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Util\ReflectionAttributeReader;
use LML\SDK\Exception\DataNotFoundException;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function trim;
use function rtrim;
use function sprintf;
use function in_array;
use function array_map;
use function get_class;
use function json_decode;
use function array_merge;
use function spl_object_hash;
use function array_key_exists;
use function React\Async\await;
use function React\Async\parallel;
use function React\Promise\resolve;

/**
 * Doctrine equivalent for models
 */
class EntityManager implements ResetInterface
{
    /**
     * @var array<string, ModelInterface>
     */
    private array $newEntities = [];

    /**
     * @var array<string, ModelInterface>
     */
    private array $entitiesToBeDeleted = [];

    /**
     * @var array<string, ModelInterface>
     */
    private array $managed = [];

    /**
     * Identity map like one in Doctrine. For example:
     *
     * <code>
     * [
     *      'SDK\Entity\Product\Product' => [
     *          1 => PromiseInterface<$product1>,  // $id => promise of $instance
     *      ],
     * ]
     * </code>
     *
     * @var array<class-string<ModelInterface>, array<non-empty-string, PromiseInterface<ModelInterface>>>
     */
    private array $identityMap = [];

    /**
     * @var array<class-string, array<string, array>>
     */
    private array $fetchedValues = [];

    /**
     * @var array<class-string<ModelInterface>, Entity>
     */
    private array $entityAttributeCache = [];

    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     */
    public function __construct(
        private ServiceLocator $repositories,
        private ClientInterface $client,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function reset(): void
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->newEntities = [];
        $this->entitiesToBeDeleted = [];
        $this->managed = [];
        $this->fetchedValues = [];
        $this->identityMap = [];
    }

    /**
     * @template TView of ModelInterface
     *
     * @param class-string<TView> $className
     *
     * @return ($await is true ? TView : PromiseInterface<?TView>)
     */
    public function findOneBy(string $className, array $filters = [], ?string $url = null, ?int $cacheTimeout = null, bool $await = false): null|ModelInterface|PromiseInterface
    {
        $paginated = $this->paginate($className, filters: $filters, url: $url, cacheTimeout: $cacheTimeout);
        $promise = $paginated->then(onFulfilled: /** @param PaginatedResults<TView> $results */ fn(PaginatedResults $results) => $results->first());

        return $await ? await($promise) : $promise;
    }

    /**
     * @template TView of ModelInterface
     * @param class-string<TView> $className
     *
     * @return PromiseInterface<list<TView>>
     */
    public function findBy(string $className, array $filters = [], ?string $url = null, int $page = 1, ?int $cacheTimeout = null): PromiseInterface
    {
        return $this->findByRecursive($className, $filters, $url, $page, cacheTimeout: $cacheTimeout);
    }

    /**
     * @template TView of ModelInterface
     * @param class-string<TView> $className
     *
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     *
     * @psalm-suppress all
     */
    public function find(string $className, string $id = null, ?string $url = null, ?int $cacheTimeout = null, bool $await = false): null|ModelInterface|PromiseInterface
    {
        if ($id && array_key_exists($id, $this->identityMap[$className] ?? [])) {
            $promise = $this->identityMap[$className][$id];

            return $await ? await($promise) : $promise;
        }

        $url ??= sprintf('%s/%s', $this->getBaseUrl($className), $id);

        $promise = $this->client->getAsync($url, cacheTimeout: $cacheTimeout, tag: $className)
            ->then(
                onFulfilled: fn(?array $data): ?ModelInterface => $data ? $this->store($className, $data) : null,
                onRejected: fn() => null,
            );

        $this->identityMap[$className][$id] = $promise;

        return $await ? await($promise) : $promise;
    }

    /**
     * @template TView of ModelInterface
     *
     * @param class-string<TView> $className
     *
     * @psalm-return ($await is true ? TView : PromiseInterface<TView>)
     *
     * @throws DataNotFoundException
     */
    public function fetch(string $className, ?string $id = null, ?string $url = null, ?int $cacheTimeout = null, bool $await = false): ModelInterface|PromiseInterface
    {
        $promise = $this->find($className, id: $id, url: $url, cacheTimeout: $cacheTimeout)->then(onFulfilled: /** @param ?TView $data */ fn(?ModelInterface $data) => $data ?: throw new DataNotFoundException());

        return $await ? await($promise) : $promise;
    }

    /**
     * @template TView of ModelInterface
     *
     * @param class-string<TView> $className
     *
     * @return ($await is true ? PaginatedResults<TView> : PromiseInterface<PaginatedResults<TView>>)
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress ArgumentTypeCoercion
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function paginate(string $className, array $filters = [], ?string $url = null, int $page = 1, ?int $limit = null, ?int $cacheTimeout = null, bool $await = false): PromiseInterface|PaginatedResults
    {
        if (!$url) {
            $url = $this->getBaseUrl($className);
            $url = rtrim($url, '/') . '/'; // Symfony trailing slash issue; this will avoid 301 redirections
        }
        /** @var PromiseInterface<null|array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<mixed>}> $promise */
        $promise = $this->client->getAsync($url, filters: $filters, page: $page, limit: $limit, tag: $className, cacheTimeout: $cacheTimeout);

        $paginationPromise = $promise
            ->then(function (?array $data) use ($className) {
                if (null === $data) {
                    return new PaginatedResults(
                        currentPage: 1,
                        nrOfPages: 1,
                        resultsPerPage: 100,
                        nextPage: null,
                        nrOfResults: 0,
                        items: [],
                    );
                }

                return new PaginatedResults(
                    currentPage: $data['current_page'] ?? 1,
                    nrOfPages: $data['nr_of_pages'] ?? 1,
                    resultsPerPage: $data['results_per_page'] ?? 10,
                    nextPage: $data['next_page'] ?? null,
                    nrOfResults: $data['nr_of_results'] ?? 0,
                    items: array_map(fn(array $item): ModelInterface => $this->store($className, $item), $data['items'] ?? []),
                );
            });

        return $await ? await($paginationPromise) : $paginationPromise;
    }

    public function persist(ModelInterface $model): void
    {
        $oid = spl_object_hash($model);
        if (isset($this->newEntities[$oid]) || isset($this->managed[$oid])) {
            return;
        }

        $this->newEntities[$oid] = $model;
    }

    public function remove(ModelInterface $model): void
    {
        $oid = spl_object_hash($model);
        if (!isset($this->managed[$oid])) {
            return;
        }

        $this->entitiesToBeDeleted[$oid] = $model;
    }

    /**
     * @throws FlushException
     *
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function flush(): void
    {
        $sortedOrderOfNewEntities = $this->getSortedOrderOfNewEntities();
        $this->eventDispatcher->dispatch(new PreFlushNewEntitiesEvent($sortedOrderOfNewEntities, $this));

        foreach ($sortedOrderOfNewEntities as $entity) {
            $this->eventDispatcher->dispatch(new PrePersistEvent($entity, $this));
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $promise = $this->client->post($baseUrl, $entity->toArray())->then(
                onFulfilled: function (ResponseInterface $response) use ($entity) {
                    $body = (string)$response->getBody();
                    $data = (array)json_decode($body, false, 512, JSON_THROW_ON_ERROR);
                    $id = (string)($data['id']);
                    $rc = new ReflectionClass($entity);
                    $property = $rc->getProperty('id');
                    $property->setAccessible(true);
                    $property->setValue($entity, $id);

                    $this->eventDispatcher->dispatch(new PostFlushEvent($entity, $this));
                },
                onRejected: function (Throwable $e) {
                    if ($e instanceof ResponseException) {
                        $body = (string)$e->getResponse()->getBody();
                        $error = (string)(json_decode($body, true)['error'] ?? $e->getMessage());
                        throw new FlushException(previous: $e, message: $error);
                    }

                    throw $e;
                }
            );
            await($promise);
        }

        $tasks = [];
        foreach ($this->managed as $entity) {
            if ($diff = $this->getChangeSet($entity)) {
                $this->eventDispatcher->dispatch(new PreUpdateEvent($entity, $this));
                $baseUrl = $this->getBaseUrl(get_class($entity));
                $tasks[] = fn(): PromiseInterface => $this->client->patch($baseUrl, $entity->getId(), $diff);
            }
        }
        foreach ($this->entitiesToBeDeleted as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $tasks[] = fn(): PromiseInterface => $this->client->delete($baseUrl, $entity->getId());
        }


        await(parallel($tasks));

        foreach ($this->newEntities as $oid => $entity) {
            $this->managed[$oid] = $entity;
        }
        // let's invalidate cache
        $tags = [];
        $all = array_merge($this->newEntities, $this->managed, $this->entitiesToBeDeleted);
        foreach ($all as $entity) {
            $className = get_class($entity);
            if (!in_array($className, $tags, true)) {
                $tags[] = $className;
            }
        }

        $this->client->invalidate(...$tags);

        $this->newEntities = [];
        $this->entitiesToBeDeleted = [];
    }

    /**
     * @param class-string $className
     */
    public function getRepository(string $className): AbstractRepository
    {
        return $this->repositories->get($className);
    }

    public function getRepositoryForModel(ModelInterface $model): AbstractRepository
    {
        $entityAttribute = $this->getEntityAttribute(get_class($model));
        $repositoryName = $entityAttribute->getRepositoryClass();

        return $this->getRepository($repositoryName);
    }

    /**
     * @param class-string<ModelInterface> $className
     */
    public function getBaseUrl(string $className): string
    {
        $attribute = $this->getEntityAttribute($className);
        $url = $attribute->getBaseUrl();

        return trim($url, '/');
    }

    /**
     * Use `object` instead of `ModelInterface`, there is a bug in psalm4
     */
    public function isNew(object $model): bool
    {
        return in_array($model, $this->newEntities, true);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getChangeSet(ModelInterface $entity): array
    {
        $className = get_class($entity);
        $fetchedValues = $this->fetchedValues[$className][$entity->getId()] ?? [];
        $currentValues = $entity->toArray();

        return array_udiff_assoc($currentValues, $fetchedValues, fn(mixed $a, mixed $b) => $a <=> $b);
    }

    /**
     * POST entities must be sync in order to populate their IDs. This approach asserts that entities are saved in correct order.
     * For the reference of the problem: Category has many Products. Product must have `category_id` value so that is why Category must be POSTed first.
     *
     * @see AbstractRepository::getPersistenceGraph()
     *
     * @return list<ModelInterface>
     */
    private function getSortedOrderOfNewEntities(): array
    {
        $sortedEntities = [];
        foreach ($this->newEntities as $newEntity) {
            $repo = $this->getRepositoryForModel($newEntity);
            foreach ($repo->getPersistenceGraph($newEntity) as $item) {
                if ($item && $this->isNew($item) && !in_array($item, $sortedEntities, true)) {
                    $sortedEntities[] = $item;
                }
            }
            if (!in_array($newEntity, $sortedEntities, true)) {
                $sortedEntities[] = $newEntity;
            }
        }
        // add any remaining, non-graphed entities, to this list
        foreach ($this->newEntities as $newEntity) {
            if (!in_array($newEntity, $sortedEntities, true)) {
                $sortedEntities[] = $newEntity;
            }
        }

        return $sortedEntities;
    }

    /**
     * @template TView of ModelInterface
     * @param class-string<TView> $className
     *
     * @param list<TView> $stored
     * @param-in list<TView> $stored
     * @param-out list<TView> $stored
     *
     * @return PromiseInterface<list<TView>>
     *
     * @psalm-suppress all
     */
    private function findByRecursive(string $className, array $filters = [], ?string $url = null, int $page = 1, ?int $cacheTimeout = null, array &$stored = []): PromiseInterface
    {
        $promise = $this->paginate($className, $filters, $url, $page, cacheTimeout: $cacheTimeout);

        return $promise->then(/** @param PaginatedResults<TView> $paginatedResults */ function (PaginatedResults $paginatedResults) use ($className, $filters, $url, $cacheTimeout, &$stored) {
            foreach ($paginatedResults->getItems() as $item) {
                $stored[] = $item;
            }
            $nextPage = $paginatedResults->getNextPage();
            if (!$nextPage) {
                return $stored;
            }

            return $this->findByRecursive($className, $filters, $url, $nextPage, $cacheTimeout, $stored);
        });
    }

    /**
     * @param class-string<ModelInterface> $className
     */
    private function store(string $className, array $data): ModelInterface
    {
        $id = (string)($data['id'] ?? null);
        if (!$id) {
            throw new LogicException('No ID found.');
        }
        if (isset($this->fetchedValues[$className][$id])) {
            foreach ($this->managed as $entity) {
                if (get_class($entity) === $className && $entity->getId() === $id) {
                    return $entity;
                }
            }
            throw new LogicException('Identity map failed.');
        }
        // find a better way but don't use $entity->toArray() as it would break async
        $this->fetchedValues[$className][$id] = $data;
        $repoName = $this->getEntityAttribute($className)->getRepositoryClass();
        $repo = $this->getRepository($repoName);
        $entity = $repo->buildOne($data);
        $this->managed[spl_object_hash($entity)] = $entity;

        $this->identityMap[$className][$id] = resolve($entity);

        return $entity;
    }

    /**
     * @param class-string<ModelInterface> $className
     */
    private function getEntityAttribute(string $className): Entity
    {
        return $this->entityAttributeCache[$className] ??= $this->doGetEntityAttribute($className);
    }

    /**
     * @param class-string<ModelInterface> $className
     */
    private function doGetEntityAttribute(string $className): Entity
    {
        return ReflectionAttributeReader::getAttribute($className, Entity::class) ?? throw new LogicException(sprintf('Model %s is not properly configured, missing %s attribute.', $className, Entity::class));
    }
}
