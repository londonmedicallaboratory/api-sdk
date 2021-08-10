<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Repository\ProductRepository;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Category\CategoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function count;

class ProductRepositoryTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var ProductRepository $repo */
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);

        $book = $repo->findOneBy(['slug' => 'book']);
        self::assertInstanceOf(ProductInterface::class, $book);
    }

    public function testPagination(): void
    {
        self::bootKernel();
        /** @var ProductRepository $repo */
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);

        $pager = $repo->findPaginated();
        self::assertGreaterThanOrEqual(1, $pager->getNbResults());
        foreach ($pager as $product) {
            self::assertInstanceOf(ProductInterface::class, $product);
        }
    }

    public function testCategories(): void
    {
        self::bootKernel();
        /** @var ProductRepository $repo */
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);
        $book = $repo->findOneBy(['slug' => 'book']);
        self::assertInstanceOf(ProductInterface::class, $book);

        $categories = $book->getCategories();
        self::assertGreaterThanOrEqual(1, count($categories));
        foreach ($categories as $category) {
            self::assertInstanceOf(CategoryInterface::class, $category);
        }

    }
}
