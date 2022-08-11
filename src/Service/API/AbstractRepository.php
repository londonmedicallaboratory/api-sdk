<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LogicException;
use RuntimeException;
use ReflectionMethod;
use ReflectionNamedType;
use React\EventLoop\Loop;
use Webmozart\Assert\Assert;
use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\PaginatedResults;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Exception\DataNotFoundException;
use LML\View\ViewFactory\AbstractViewFactory;
use function sprintf;
use function Clue\React\Block\await;

/**
 * @template TData
 * @template TView of ModelInterface
 * @template TFilters of array
 *
 * @extends AbstractViewFactory<TData, TView, array, array>
 */
abstract class AbstractRepository extends AbstractViewFactory
{
    /**
     * @var array<string, TView>
     */
    private array $cache = [];

    /**
     * @var null|class-string<TView>
     */
    private ?string $tViewClassName = null;

    private ?ClientInterface $client = null;

    private ?EntityManager $entityManager = null;

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function find(string $id, bool $await = false)
    {
        $className = $this->extractTView();

        return $this->getEntityManager()->find($className, $id, $await);
    }

    /**
     * @return ($await is true ? PaginatedResults<TView> : PromiseInterface<PaginatedResults<TView>>)
     *
     * @noinspection PhpDocSignatureInspection Bug in PHPStorm
     *
     * psalm-suppress all
     */
    public function paginate(array $filters = [], ?string $url = null, int $page = 1, bool $await = false): PromiseInterface|PaginatedResults
    {
        $className = $this->extractTView();

        return $this->getEntityManager()->paginate($className, $filters, $url, $page, $await);
    }

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidArgument
     */
    public function findOneByUrl(string $url, array $filters = [], bool $await = false)
    {
        $client = $this->getClient();

        $promise = $client->getAsync($url, $filters, cacheTimeout: $this->getCacheTimeout())
            ->then(/** @param null|TData $data */ function (null|array $data) {
                if (null === $data) {
                    return null;
                }
                $id = $data['id'] ?? null;
                if (!$id) {
                    return null;
                }

                return $this->cache[(string)$id] ??= $this->buildOne($data);
            });

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * Retrieve one entity, and throw Exception if none found.
     *
     * @return PromiseInterface<TView>
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgumentTypeCoercion
     * @psalm-suppress InvalidArgument
     */
    public function fetchOneBy(string $url, array $filters = []): PromiseInterface
    {
        $client = $this->getClient();

        /** @var PromiseInterface<TData> $promise */
        $promise = $client->getAsync($url, $filters, 1, cacheTimeout: $this->getCacheTimeout());

        return $promise->then(function (array $data) {
            $id = $data['id'] ?? throw new DataNotFoundException();

            return $this->cache[(string)$id] ??= $this->buildOne($data);
        });
    }

    /**
     * @psalm-return ($await is true ? TView : PromiseInterface<TView>)
     *
     * @psalm-suppress MixedArrayAccess
     */
    public function findOrThrowException(string $id, bool $await = false)
    {
        $url = sprintf('%s/%s', $this->getBaseUrl(), $id);
        $client = $this->getClient();

        $promise = $client->getAsync($url, cacheTimeout: $this->getCacheTimeout())
            ->then(function ($data) {
                if (!$data) {
                    throw new RuntimeException();
                }
                $id = (string)$data['id'];

                /** @psalm-suppress MixedArgument */
                return $this->cache[$id] ??= $this->buildOne($data);
            });

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * @param TFilters $filters
     *
     * @return LazyValueInterface<?TView>
     */
    public function findLazy(array $filters = [], ?string $url = null): LazyValueInterface
    {
        $promise = $this->findOneByDeprecated($filters, $url);

        return new LazyPromise($promise);
    }

    /**
     * Finding one by slug is mostly used query so this method is just a shortcut.
     *
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function findOneBySlug(string $slug, bool $await = false)
    {
        $promise = $this->findOneByDeprecated(['slug' => $slug]);

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function findOneByDeprecated(array $filters = [], ?string $url = null, bool $await = false)
    {
        $paginated = $this->paginate($filters, $url);

        $promise = $paginated->then(function (PaginatedResults $results) {
            return $results->first();
        });

        return $await ? await($promise, Loop::get()) : $promise;
    }

    /**
     * @psalm-return ($await is true ? list<TView> : PromiseInterface<list<TView>>)
     */
    public function findAll(bool $await = false)
    {
        $promise = $this->findBy();

        return $await ? await($promise, Loop::get()) : $promise;
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
    public function findBy(array $filters = [], ?string $url = null, int $page = 1, array &$stored = []): PromiseInterface
    {
        $promise = $this->paginate($filters, $url, $page);

        return $promise->then(function (PaginatedResults $paginatedResults) use ($filters, $url, &$stored) {
            /** @noinspection MissUsingForeachInspection */
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
     * @param TFilters $filters
     */
    public function findFromUrl(string $url, array $filters = []): PromiseInterface
    {
        return $this->findBy(filters: $filters, url: $url);
    }

    /**
     * @param TView $model
     */
    public function persist(ModelInterface $model): void
    {
        $this->getEntityManager()->persist($model);
    }

    public function clear(): void
    {
        $this->getEntityManager()->clear();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager ?? throw new RuntimeException('Entity manager is not defined.');
    }

    /**
     * Used by Symfony
     *
     * @noinspection PhpUnused
     */
    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    protected function getClient(): ClientInterface
    {
        return $this->client ?? throw new RuntimeException('Client is not defined.');
    }

    /**
     * Used by Symfony
     *
     * @noinspection PhpUnused
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    protected function getBaseUrl(): string
    {
        $tViewClassName = $this->extractTView();

        return $this->getEntityManager()->getBaseUrl($tViewClassName);
    }

    /**
     * Returning null means default timeout will be used, i.e. the one defined in ``lml_sdk.cache_expiration``
     */
    protected function getCacheTimeout(): ?int
    {
        return null;
    }

    /**
     * Returns class-name by using reflection; calculated only once.
     *
     * @return class-string<TView>
     */
    private function extractTView(): string
    {
        return $this->tViewClassName ??= $this->doExtractTViewClassName();
    }

    /**
     * @return class-string<TView>
     *
     * We can "thank" PHP for lack of generics:
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function doExtractTViewClassName(): string
    {
        $rc = new ReflectionMethod($this, 'one');
        $returnType = $rc->getReturnType() ?? throw new LogicException(sprintf('You **must** typehint return value of \'one\' method in \'%s\'.', get_class($this)));

        Assert::isInstanceOf($returnType, ReflectionNamedType::class);
        Assert::classExists($name = $returnType->getName());

        if (!is_a($name, ModelInterface::class, true)) {
            throw new LogicException(sprintf('Class \'%s\' must implement \'%s\'.', $name, ModelInterface::class));
        }

        return $name;
    }
}
