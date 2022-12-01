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

    /**
     * @return list<self>
     */
    public static function getRefundTypes(): array
    {
        return [
            self::REFUND_REQUESTED,
            self::REFUND_COMPLETE,
            self::REFUND_DENIED,
        ];
    }

    /**
     * @return list<self>
     */
    public static function getPaymentTypes(): array
    {
        return [
            self::INVOICED,
            self::AWAITING_PAYMENT,
            self::PAID,
            self::PAYMENT_FAILED,
        ];
    }

    public function isRefund(): bool
    {
        return in_array($this, self::getRefundTypes(), true);
    }

    public function isPayment(): bool
    {
        return in_array($this, self::getPaymentTypes(), true);
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
