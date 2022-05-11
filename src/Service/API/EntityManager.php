<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Service\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Doctrine equivalent for models
 */
class EntityManager
{
    /**
     * @var array<class-string, array<string, ModelInterface>>
     */
    private array $identityMap = [];

    private array $newEntities = [];

    private array $entitiesToBeDeleted = [];

    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     *
     * @noinspection PhpPropertyOnlyWrittenInspection
     */
    public function __construct(
        private ServiceLocator   $repositories,
        private ?ClientInterface $client,
    )
    {
    }

    public function persist(ModelInterface $model): void
    {
        if ($this->isManaged($model)) {
            return;
        }
        $class = get_class($model);
        $this->identityMap[$class][$model->getId()] = $model;
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
}
