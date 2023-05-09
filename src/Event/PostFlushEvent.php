<?php

declare(strict_types=1);

namespace LML\SDK\Event;

use LML\SDK\Entity\ModelInterface;
use LML\SDK\Service\API\EntityManager;
use Symfony\Contracts\EventDispatcher\Event;

class PostFlushEvent extends Event
{
    public function __construct(
        private ModelInterface $entity,
        private EntityManager $entityManager,
    )
    {
    }

    public function getEntity(): ModelInterface
    {
        return $this->entity;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}
