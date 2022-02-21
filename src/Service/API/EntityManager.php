<?php

declare(strict_types=1);

namespace LML\SDK\Service\API;

use LML\SDK\Service\API\AbstractRepository;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Doctrine equivalent for models
 */
class EntityManager
{
    /**
     * @param ServiceLocator<class-string, AbstractRepository> $repositories
     */
    public function __construct(
        private ServiceLocator $repositories,
    )
    {
    }

    /**
     * @param class-string $className
     */
    public function getRepository(string $className): AbstractRepository
    {
        return $this->repositories->get($className);
    }
}
