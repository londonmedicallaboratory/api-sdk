<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Enum\Model\NameableInterface;

/**
 * @link https://github.com/londonmedicallaboratory/commando/issues/217
 */
enum PaymentTypeEnum: string implements NameableInterface
{
    case REFUND_REQUESTED = 'refund_requested';
    case REFUND_COMPLETE = 'refund_complete';
    case REFUND_DENIED = 'refund_denied';

    case INVOICED = 'invoiced';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case PAID = 'paid';
    case PAYMENT_FAILED = 'payment_failed';

    public function isRefund(): bool
    {
        $allowed = [self::REFUND_REQUESTED, self::REFUND_COMPLETE, self::REFUND_DENIED];

        return in_array($this, $allowed, true);
    }

    public function isPayment(): bool
    {
        $allowed = [self::INVOICED, self::AWAITING_PAYMENT, self::PAID, self::PAYMENT_FAILED];

        return in_array($this, $allowed, true);
    }

    public function getName(): string
    {
        return match ($this) {
            self::REFUND_REQUESTED => 'Refund requested',
            self::REFUND_COMPLETE => 'Refund complete',
            self::REFUND_DENIED => 'Refund denied',
            self::INVOICED => 'Invoiced',
            self::AWAITING_PAYMENT => 'Awaiting payment',
            self::PAID => 'Paid',
            self::PAYMENT_FAILED => 'Payment failed',
        };
    }
}
