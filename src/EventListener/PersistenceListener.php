<?php

declare(strict_types=1);

namespace LML\SDK\EventListener;

use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\Basket\Basket;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Event\PreFlushNewEntitiesEvent;
use LML\SDK\Entity\Appointment\Appointment;
use LML\SDK\Exception\PersistenceNotAllowedException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use function sprintf;
use function get_class;

/**
 * @see Order::$initialAppointment
 * @see Basket::$initialAppointment
 *
 * Assure that $initialAppointment cannot be persisted.
 * The reason is to avoid creating useless appointments on commando-end in case user persists it by accident.
 */
class PersistenceListener
{
    #[AsEventListener]
    public function preFlush(PreFlushNewEntitiesEvent $event): void
    {
        $entityManager = $event->getEntityManager();
        $entities = $event->getEntitiesToBeFlushed();
        foreach ($entities as $entity) {
            $initialAppointment = $this->extractInitialAppointmentFromEntity($entity);
            if ($initialAppointment && $entityManager->isNew($initialAppointment)) {
                throw new PersistenceNotAllowedException(sprintf('You are not allowed to persist \'initialAppointment\' on \'%s\' entity.', get_class($entity)));
            }
        }
    }

    /**
     * Going around psalm4 issue
     *
     * @todo Fix this when psalm5 is implemented
     */
    private function extractInitialAppointmentFromEntity(ModelInterface $entity): ?Appointment
    {
        if ($entity instanceof Order || $entity instanceof Basket) {
            return $entity->getInitialAppointment();
        }

        return null;
    }
}
