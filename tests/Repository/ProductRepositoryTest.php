<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Product\Product;
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

    public function testProductFiles(): void
    {
        self::bootKernel();

        $product = $this->getProductRepository()->findOneBySlug('book', true);
        self::assertInstanceOf(Product::class, $product);
    }

    private function getProductRepository(): ProductRepository
    {
        return $this->getService(ProductRepository::class);
    }
}
