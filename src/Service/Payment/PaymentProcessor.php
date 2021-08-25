<?php

declare(strict_types=1);

namespace LML\SDK\Service\Payment;

use LML\SDK\DTO\Payment;
use LML\SDK\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
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

    public function confirm(string $name, Payment $configuration): ?Response
    {
        $strategy = $this->getStrategy($name);

        return $strategy->confirm($configuration);
    }

    public function pay(string $name, Payment $configuration): ?Response
    {
        $strategy = $this->getStrategy($name);

        return $strategy->pay($configuration);
    }

    private function getStrategy(string $name): PaymentProcessorStrategyInterface
    {
        return $this->strategies->get($name);
    }
}
