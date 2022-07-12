<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Repository\OrderRepository;

class ReadOrderTest extends AbstractTest
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        $repo = $this->getOrderRepository();
        $pagination = $repo->paginate(await: true);
        self::assertNotEmpty($pagination->getItems(), 'No fixtures');
    }

    private function getOrderRepository(): OrderRepository
    {
        return $this->getService(OrderRepository::class);
    }
}
