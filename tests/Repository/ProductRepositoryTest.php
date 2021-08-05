<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    public function testClient(): void
    {
        self::bootKernel();

        $repo = self::$kernel->getContainer()->get('lml_api.product_repository');
        $repo->find('qwe');

    }
}
