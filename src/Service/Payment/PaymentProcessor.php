<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment;

use LML\SDK\DTO\Payment;
use LML\SDK\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use LML\SDK\Exception\PaymentFailureException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ServiceLocator;
use LML\SDK\Service\Payment\Tagged\PaymentProcessorStrategyInterface;

class PaymentProcessor
{
    /**
     * @param ServiceLocator<string, PaymentProcessorStrategyInterface> $strategies
     */
    public function __construct(
        private ServiceLocator $strategies,
        private OrderRepository $orderRepository,
    )
    {
    }

    /**
     * @no-named-arguments
     */
    public function pay(string $name, Payment $payment): ?Response
    {
        try {
            return $this->strategies->get($name)->pay($payment);
        } catch (PaymentFailureException $e) {
            return $this->handlePaymentFailureException($e, $payment);
        }
    }

    /**
     * @no-named-arguments
     */
    public function confirm(string $name, Payment $payment): ?Response
    {
        try {
            return $this->strategies->get($name)->confirm($payment);
        } catch (PaymentFailureException $e) {
            return $this->handlePaymentFailureException($e, $payment);
        }
    }

    /**
     * When payment has failed, try to handle it in this order:
     *
     * - if there is a callback ``paymentExceptionHandler`` registered, run it first
     * - if there is a registered ``failureUrl``, redirect to it
     *
     * If all fails, throw it and let ExceptionListener take care of it, or 500
     */
    private function handlePaymentFailureException(PaymentFailureException $e, Payment $payment): ?Response
    {
        $handler = $payment->paymentExceptionHandler;
        if ($handler) {
            return $handler($e);
        }
        if ($failureUrl = $payment->failureUrl) {
            return new RedirectResponse($failureUrl);
        }
        throw $e;
    }
}
