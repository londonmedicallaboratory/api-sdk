<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Repository\OrderRepository;
use LML\SDK\Entity\Order\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReadOrderTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var OrderRepository $repo */
        $repo = self::$kernel->getContainer()->get(OrderRepository::class);

        $lazy = new LazyPromise($repo->find('45274c8e-8cb4-451e-a3ed-b6d800176a80'));
        $order = $lazy->getValue();
        self::assertInstanceOf(OrderInterface::class, $order);
//        dump($order->getTotal());
    }
}
