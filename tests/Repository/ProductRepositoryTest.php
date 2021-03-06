<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\ProductRepository;

class ProductRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getProductRepository()->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    private function getProductRepository(): ProductRepository
    {
        return $this->getService(ProductRepository::class);
    }
}
