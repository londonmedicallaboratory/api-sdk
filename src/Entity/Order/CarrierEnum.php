<?php
declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Enum\Model\NameableInterface;

enum CarrierEnum: string implements NameableInterface
{
    case ROYAL_MAIL = 'royal_mail';

    public function getName(): string
    {
        return match ($this) {
            self::ROYAL_MAIL => 'Royal Mail',
        };
    }
}
