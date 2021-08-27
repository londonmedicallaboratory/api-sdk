<?php

declare(strict_types=1);

namespace LML\SDK\Service\Model;

use LML\SDK\ViewFactory\AbstractViewRepository;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Doctrine equivalent for models
 */
class ModelManager
{
    /**
     * @param ServiceLocator<class-string, AbstractViewRepository> $repositories
     */
    public function __construct(
        private ServiceLocator $repositories,
    )
    {
    }

    /**
     * @param class-string $className
     */
    public function getRepository(string $className): AbstractViewRepository
    {
        return $this->repositories->get($className);
    }
}
