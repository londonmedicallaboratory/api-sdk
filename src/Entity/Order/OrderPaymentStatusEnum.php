<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Enum\Model\NameableInterface;

/**
 * @see https://github.com/londonmedicallaboratory/commando/issues/307
 */
enum OrderPaymentStatusEnum: string implements NameableInterface
{
    case PAID = 'paid';
    case REFUND_REQUESTED = 'refund_requested';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';
    case TO_INVOICE = 'to_invoice';
    case INVOICED = 'invoiced';

    public function getName(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::REFUND_REQUESTED => 'Refund requested',
            self::REFUNDED => 'Refunded',
            self::CANCELLED => 'Cancelled',
            self::TO_INVOICE => 'To invoice',
            self::INVOICED => 'Invoiced',
        };
    }
}
