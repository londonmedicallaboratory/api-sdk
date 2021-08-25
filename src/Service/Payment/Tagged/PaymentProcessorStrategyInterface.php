<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment\Tagged;

use LML\SDK\DTO\Payment;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @psalm-internal LML\SDK\Service\Payment
 */
interface PaymentProcessorStrategyInterface
{
    public static function getName(): string;

    public function pay(Payment $payment): ?Response;

    public function confirm(Payment $payment): ?Response;
}
