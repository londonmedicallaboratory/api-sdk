<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Entity\Category\Category;
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

    public function testFindOneBySlug(): void
    {
        self::bootKernel();

        $product = $this->getProductRepository()->findOneBySlug('book', true);
        self::assertInstanceOf(Product::class, $product);

        $categories = $product->getCategories();
        self::assertNotEmpty($categories);
        foreach ($categories as $category) {
            self::assertInstanceOf(Category::class, $category);
        }
    }

    public function testFetchOneBy(): void
    {
        self::bootKernel();
        $product = $this->getProductRepository()->fetch('8b94cbda-5eee-48c6-a741-4995ef2d71c5', await: true);
        self::assertInstanceOf(Product::class, $product);
    }

    private function getProductRepository(): ProductRepository
    {
        return $this->getService(ProductRepository::class);
    }
}
