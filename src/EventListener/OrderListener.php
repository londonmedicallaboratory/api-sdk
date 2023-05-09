<?php

declare(strict_types=1);

namespace LML\SDK\EventListener;

use LML\SDK\Entity\Order\Order;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Exception\FlushException;
use LML\SDK\Event\PreFlushNewEntitiesEvent;
use LML\SDK\Entity\Appointment\Appointment;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Assure that Appointment has not been persisted, if used in Order::initialAppointment
 * The reason is to avoid creating useless appointments on commando-end in case user persists it by accident.
 */
class OrderListener
{
    #[AsEventListener]
    public function preFlush(PreFlushNewEntitiesEvent $event): void
    {
        $entityManager = $event->getEntityManager();
        $entities = $event->getEntitiesToBeFlushed();
        foreach ($entities as $entity) {
            $initialAppointment = $this->extractInitialAppointmentFromOrder($entity);
            if ($initialAppointment && $entityManager->isNew($initialAppointment)) {
                throw new FlushException('You are not allowed to persist \'Order::initialAppointment\'.');
            }
        }
    }

    /**
     * Going around psalm4 issue
     *
     * @todo Fix this when psalm5 is implemented
     */
    private function extractInitialAppointmentFromOrder(ModelInterface $entity): ?Appointment
    {
        if ($entity instanceof Order) {
            return $entity->getInitialAppointment();
        }

        return null;
    }
}
