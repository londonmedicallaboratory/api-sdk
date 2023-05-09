<?php

declare(strict_types=1);

namespace LML\SDK\Event;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Service\API\EntityManager;
use Symfony\Contracts\EventDispatcher\Event;

class PreFlushNewEntitiesEvent extends Event
{
    /**
     * @param list<ModelInterface> $entitiesToBeFlushed
     */
    public function __construct(
        private array $entitiesToBeFlushed,
        private EntityManager $entityManager,
    )
    {
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @return list<ModelInterface>
     */
    public function getEntitiesToBeFlushed(): array
    {
        return $this->entitiesToBeFlushed;
    }
}
