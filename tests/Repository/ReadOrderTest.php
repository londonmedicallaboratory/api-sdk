<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Entity\Order\OrderInterface;

class ReadOrderTest extends AbstractTest
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        $repo = $this->getOrderRepository();

        $lazy = new LazyPromise($repo->find('45274c8e-8cb4-451e-a3ed-b6d800176a80'));
        $order = $lazy->getValue();
        self::assertInstanceOf(OrderInterface::class, $order);
    }

    private function getOrderRepository(): OrderRepository
    {
        return $this->getService(OrderRepository::class);
    }
}
