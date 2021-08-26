<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment\Tagged;

use LML\SDK\DTO\Payment;
use LML\SDK\Service\Payment\PaymentProcessor;
use Symfony\Component\HttpFoundation\Response;
use LML\SDK\Exception\PaymentFailureException;

/**
 * **Never** use any of the implementations directly.
 *
 * @see PaymentProcessor is the only allowed entry-point
 */
interface PaymentProcessorStrategyInterface
{
    public static function getName(): string;

    /**
     * @throws PaymentFailureException
     */
    public function pay(Payment $payment): ?Response;

    /**
     * @throws PaymentFailureException
     */
    public function confirm(Payment $payment): ?Response;
}
