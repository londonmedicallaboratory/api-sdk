<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LogicException;
use ReflectionClass;
use React\EventLoop\Loop;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\PaginatedResults;
use Psr\Http\Message\ResponseInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Util\ReflectionAttributeReader;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;

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
     * @var array<class-string, array<string, array>>
     */
    private array $fetchedValues = [];

    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     */
    public function __construct(
        private ServiceLocator  $repositories,
        private ClientInterface $client,
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
    }

    /**
     * @template TView of ModelInterface
     * @param class-string<TView> $className
     *
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     *
     * @psalm-suppress all
     */
    public function find(string $className, string $id, bool $await = false): null|ModelInterface|PromiseInterface
    {
        $url = sprintf('%s/%s', $this->getBaseUrl($className), $id);
        $client = $this->client;

        $promise = $client->getAsync($url, cacheTimeout: 30, tag: $className)
            ->then(function ($data) use ($className) {
                if (!$data) {
                    return null;
                }

                return $this->store($className, $data);
            }, onRejected: function () {
                return null;
            });

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * @template TPag of ModelInterface
     * @param class-string<TPag> $className
     *
     * @return ($await is true ? PaginatedResults<TPag> : PromiseInterface<PaginatedResults<TPag>>)
     *
     * @noinspection PhpDocSignatureInspection Bug in PHPStorm
     *
     * @psalm-suppress all
     */
    public function paginate(string $className, array $filters = [], ?string $url = null, int $page = 1, bool $await = false): PromiseInterface|PaginatedResults
    {
        $client = $this->client;
        if (!$url) {
            $url = $this->getBaseUrl($className);
            $url = rtrim($url, '/') . '/'; // Symfony trailing slash issue; this will avoid 301 redirections
        }
        /** @var PromiseInterface<array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<mixed>}> $promise */
        $promise = $client->getAsync($url, filters: $filters, page: $page, tag: $className);

        $paginationPromise = $promise
            ->then(function (array $data) use ($className) {
                $views = [];
                $items = $data['items'] ?? [];
                foreach ($items as $item) {
                    $view = $this->store($className, $item);
                    $views[] = $view;
                }

                return new PaginatedResults(
                    currentPage   : $data['current_page'] ?? 1,
                    nrOfPages     : $data['nr_of_pages'] ?? 1,
                    resultsPerPage: $data['results_per_page'] ?? 10,
                    nextPage      : $data['next_page'] ?? null,
                    nrOfResults   : $data['nr_of_results'] ?? 0,
                    items         : $views,
                );
            });

        return $await ? await($paginationPromise, Loop::get()) : $paginationPromise;
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

    public function flush(): void
    {
        $promises = [];
        foreach ($this->newEntities as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));

            $promises[] = $this->client->post($baseUrl, $entity->toArray())->then(function (ResponseInterface $response) use ($entity) {
                $body = (string)$response->getBody();
                $data = (array)json_decode($body, false, 512, JSON_THROW_ON_ERROR);
                $id = (string)($data['id']);
                $rc = new ReflectionClass($entity);
                $property = $rc->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($entity, $id);
            });
        }

        foreach ($this->managed as $entity) {
            $fetchedValues = $this->fetchedValues[get_class($entity)][$entity->getId()] ?? [];
            // no changes to entity
            if ($fetchedValues === $entity->toArray()) {
                continue;
            }
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $promises[] = $this->client->patch($baseUrl, $entity->getId(), $entity->toArray());
        }

        foreach ($this->entitiesToBeDeleted as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $promises[] = $this->client->delete($baseUrl, $entity->getId());
        }

        awaitAll($promises);

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

    /**
     * @param class-string $className
     */
    private function store(string $className, array $data): ModelInterface
    {
        $id = (string)($data['id'] ?? throw new LogicException('No ID found.'));

        $this->fetchedValues[$className][$id] = $data;
        $repoName = $this->getEntityAttribute($className)->getRepositoryClass();
        $repo = $this->getRepository($repoName);
        $entity = $repo->buildOne($data);
        $this->managed[spl_object_hash($entity)] = $entity;

        return $entity;
    }

    /**
     * @param class-string $className
     */
    private function getEntityAttribute(string $className): Entity
    {
        return ReflectionAttributeReader::getAttribute($className, Entity::class) ?? throw new LogicException(sprintf('Model %s is not properly configured, missing %s attribute.', $className, Entity::class));
    }

    /**
     * @param class-string $className
     */
    private function getBaseUrl(string $className): string
    {
        $attribute = ReflectionAttributeReader::getAttribute($className, Entity::class);

        $url = $attribute?->getBaseUrl() ?? throw new LogicException(sprintf('Model %s is not properly configured, missing %s attribute.', $className, Entity::class));

        return trim($url, '/');
    }
}
