<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Order;

use LML\SDK\Enum\Model\NameableInterface;

/**
 * @see https://github.com/londonmedicallaboratory/commando/issues/307
 */
enum OrderShippingStatusEnum: string implements NameableInterface
{
    case AWAITING_SHIPPING = 'awaiting_shipping';
    case MANIFESTED = 'manifested';
    case MANIFEST_FAILED = 'manifest_failed';
    case SHIPPED = 'shipped';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case DELIVERY_FAILED = 'delivery_failed';
    case RETURNED = 'returned';
    case APPOINTMENT = 'appointment';

    public function getName(): string
    {
        return match ($this) {
            self::AWAITING_SHIPPING => 'Awaiting shipping',
            self::MANIFESTED => 'Manifested',
            self::MANIFEST_FAILED => 'Manifest failed',
            self::SHIPPED => 'Shipped',
            self::OUT_FOR_DELIVERY => 'Out for delivery',
            self::DELIVERED => 'Delivered',
            self::DELIVERY_FAILED => 'Delivery failed',
            self::RETURNED => 'Returned',
            self::APPOINTMENT => 'Appointment',
        };
    }
}
