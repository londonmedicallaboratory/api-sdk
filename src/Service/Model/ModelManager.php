<?php

declare(strict_types=1);

namespace LML\SDK\Service\Model;

use LML\SDK\Service\Model\AbstractRepository;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Doctrine equivalent for models
 */
class ModelManager
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
