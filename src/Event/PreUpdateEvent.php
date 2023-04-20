<?php

declare(strict_types=1);

namespace LML\SDK\Event;

use LML\SDK\Entity\ModelInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PreUpdateEvent extends Event
{
    public function __construct(
        private ModelInterface $entity,
    )
    {
    }

    public function getEntity(): ModelInterface
    {
        return $this->entity;
    }
}
