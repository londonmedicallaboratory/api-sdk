<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LogicException;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Service\Client\ClientInterface;
use LML\SDK\Util\ReflectionAttributeReader;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Clue\React\Block\awaitAll;

/**
 * Doctrine equivalent for models
 */
class EntityManager
{
    /**
     * @var array<class-string, array<string, ModelInterface>>
     */
    private array $identityMap = [];

    /**
     * @var array<array-key, ModelInterface>
     */
    private array $newEntities = [];

    /**
     * @var array<array-key, ModelInterface>
     */
    private array $entitiesToBeDeleted = [];

    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     */
    public function __construct(
        private ServiceLocator  $repositories,
        private ClientInterface $client,
    )
    {
    }

    public function remove(ModelInterface $model): void
    {
        if (!in_array($model, $this->entitiesToBeDeleted, true)) {
            $this->entitiesToBeDeleted[] = $model;
        }
    }

    public function persist(ModelInterface $model): void
    {
        if ($this->isManaged($model)) {
            return;
        }
        $class = get_class($model);
        $this->identityMap[$class][$model->getId()] = $model;
        $this->newEntities[] = $model;
    }

    public function flush(): void
    {
        $promises = [];
        foreach ($this->newEntities as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $baseUrl = trim('/', $baseUrl);
            $promises[] = $this->client->post($baseUrl, $entity->toArray());
        }

        foreach ($this->entitiesToBeDeleted as $entity) {
            $baseUrl = $this->getBaseUrl(get_class($entity));
            $baseUrl = trim('/', $baseUrl);
            $promises[] = $this->client->delete($baseUrl, $entity->getId());
        }

        awaitAll($promises);
    }

    public function isManaged(ModelInterface $model): bool
    {
        $id = $model->getId();
        if (!$id) {
            return false;
        }
        $class = get_class($model);

        return isset($this->identityMap[$class][$id]);
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
    private function getBaseUrl(string $className): string
    {
        $attribute = ReflectionAttributeReader::getAttribute($className, Entity::class);

        return $attribute?->getBaseUrl() ?? throw new LogicException(sprintf('Model %s is not properly configured, missing %s attribute.', $className, Entity::class));
    }
}
