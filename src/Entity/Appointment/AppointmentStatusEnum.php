<?php
declare(strict_types=1);

namespace LML\SDK\Entity\Appointment;

use LML\SDK\Enum\Model\NameableInterface;

enum AppointmentStatusEnum: string implements NameableInterface
{
    case BOOKED = 'booked';
    case ATTENDED = 'attended';
    case NO_SHOW = 'no_show';
    case CANCELLED = 'cancelled';

    public function getName(): string
    {
        return match ($this) {
            self::BOOKED => 'Booked',
            self::ATTENDED => 'Attended',
            self::NO_SHOW => 'No show',
            self::CANCELLED => 'Cancelled',
        };
    }
}
