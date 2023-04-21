<?php

declare(strict_types=1);

namespace LML\SDK\Enum;

use LML\SDK\Enum\Model\NameableInterface;

/**
 * @link https://github.com/londonmedicallaboratory/commando/issues/112
 *
 * awaiting_shipping
 * manifested
 * shipped
 * out_for_delivery
 * delivered
 * failed_delivery
 */
enum OrderStatusEnum: string implements NameableInterface
{
    case AWAITING_PAYMENT = 'awaiting_payment';
    case AWAITING_SHIPPING = 'awaiting_shipping';
    case MANIFESTED = 'manifested';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVER = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case DELIVERY_FAILED = 'delivery_failed';
    case CANCELLED = 'cancelled';

    // @todo Find out what these are used for
    case MANIFEST_FAILED = 'manifest_failed';

    case PENDING_REFUND = 'pending_refund';
    case REFUNDED = 'refunded';

    public function getName(): string
    {
        return match ($this) {
            self::AWAITING_PAYMENT => 'Awaiting payment',
            self::MANIFESTED => 'Manifested',
            self::AWAITING_SHIPPING => 'Awaiting shipping',
            self::DELIVERED => 'Delivered',
            self::PENDING_REFUND => 'Pending refund',
            self::REFUNDED => 'Refunded',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVER => 'Out for deliver',
            self::DELIVERY_FAILED => 'Delivery failed',
            self::CANCELLED => 'Cancelled',
            self::MANIFEST_FAILED => 'Manifest failed',
        };
    }
}
