<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderUpdateTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var OrderRepository $repo */
        $repo = self::$kernel->getContainer()->get(OrderRepository::class);

        $promise = $repo->patchId('04b3f13a-27c0-438e-a977-ac2f617ca3b0', []);
    }
}
