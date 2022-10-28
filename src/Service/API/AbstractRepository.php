<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LogicException;
use RuntimeException;
use ReflectionMethod;
use ReflectionNamedType;
use Pagerfanta\Pagerfanta;
use Webmozart\Assert\Assert;
use LML\SDK\Pager\PromiseAdapter;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Exception\DataNotFoundException;
use LML\View\ViewFactory\AbstractViewFactory;
use function sprintf;
use function React\Promise\resolve;
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
     * @var null|class-string<TView>
     */
    private ?string $tViewClassName = null;

    private ?ClientInterface $client = null;

    private ?EntityManager $entityManager = null;

    /**
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function find(?string $id, bool $await = false): null|ModelInterface|PromiseInterface
    {
        if (!$id) {
            return $await ? null : resolve();
        }

        return $this->getEntityManager()->find(className: $this->extractTView(), id: $id, cacheTimeout: $this->getCacheTimeout(), await: $await);
    }

    /**
     * @psalm-return ($await is true ? TView : PromiseInterface<TView>)
     */
    public function fetch(?string $id, bool $await = false): ModelInterface|PromiseInterface
    {
        if (!$id) {
            throw new DataNotFoundException();
        }

        return $this->getEntityManager()->fetch(className: $this->extractTView(), id: $id, cacheTimeout: $this->getCacheTimeout(), await: $await);
    }

    /**
     * @return ($await is true ? null|TView : PromiseInterface<null|TView>)
     */
    public function findOneBy(array $filters = [], ?string $url = null, bool $await = false): null|ModelInterface|PromiseInterface
    {
        $paginated = $this->paginate($filters, $url, limit: 1);
        $promise = $paginated->then(fn(PaginatedResults $results) => $results->first());

        return $await ? await($promise) : $promise;
    }

    /**
     * Finding one by slug is mostly used query so this method is just a shortcut.
     *
     * @psalm-return ($await is true ? null|TView : PromiseInterface<?TView>)
     */
    public function findOneBySlug(string $slug, bool $await = false): null|ModelInterface|PromiseInterface
    {
        $className = $this->extractTView();
        $promise = $this->getEntityManager()->findOneBy($className, ['slug' => $slug], cacheTimeout: $this->getCacheTimeout());

        return $await ? await($promise) : $promise;
    }

    /**
     * Retrieve one entity, or throw Exception if none found.
     *
     * @return ($await is true ? TView : PromiseInterface<TView>)
     */
    public function fetchOneBy(?string $url = null, array $filters = [], bool $await = false): ModelInterface|PromiseInterface
    {
        $promise = $this->getEntityManager()->fetch($this->extractTView(), url: $url, cacheTimeout: $this->getCacheTimeout());

        return $await ? await($promise) : $promise;
//        $paginated = $this->paginate(filters: $filters, url: $url, limit: 1);
//        $promise = $paginated->then(fn(PaginatedResults $results) => $results->first() ?? throw new DataNotFoundException());
//
//        return $await ? await($promise) : $promise;
    }

    /**
     * @return ($await is true ? list<TView> : PromiseInterface<list<TView>>)
     */
    public function findAll(bool $await = false): array|PromiseInterface
    {
        return $this->findBy(await: $await);
    }

    /**
     * @return ($await is true ? list<TView> : PromiseInterface<list<TView>>)
     */
    public function findBy(array $filters = [], ?string $url = null, int $page = 1, bool $await = false): array|PromiseInterface
    {
        $promise = $this->getEntityManager()->findBy(className: $this->extractTView(), filters: $filters, url: $url, page: $page, cacheTimeout: $this->getCacheTimeout());

        return $await ? await($promise) : $promise;
    }

    /**
     * @return Pagerfanta<TView>
     */
    public function pagerfanta(array $filters = [], ?string $url = null, int $page = 1, ?int $limit = null): Pagerfanta
    {
        $promise = $this->paginate($filters, $url, $page, $limit);
        $adapter = new PromiseAdapter($promise);

        return new Pagerfanta($adapter);
    }

    /**
     * @return ($await is true ? PaginatedResults<TView> : PromiseInterface<PaginatedResults<TView>>)
     */
    public function paginate(array $filters = [], ?string $url = null, int $page = 1, ?int $limit = null, bool $await = false): PromiseInterface|PaginatedResults
    {
        return $this->getEntityManager()->paginate(className: $this->extractTView(), filters: $filters, url: $url, page: $page, limit: $limit, await: $await, cacheTimeout: $this->getCacheTimeout());
    }

    /**
     * @param TView $model
     */
    public function persist(ModelInterface $model): void
    {
        $this->getEntityManager()->persist($model);
    }

    /**
     * @param TView $model
     */
    public function remove(ModelInterface $model): void
    {
        $this->getEntityManager()->remove($model);
    }

    public function clear(): void
    {
        $this->getEntityManager()->clear();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    protected function getEntityManager(): EntityManager
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
