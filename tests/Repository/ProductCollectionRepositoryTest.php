<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\ProductCollectionRepository;

class ProductCollectionRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getProductCollectionRepository()->paginate(await: true);
        foreach ($pagination->getItems() as $item) {
            dump($item->getLogo());
        }
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

//    public function testProductFiles(): void
//    {
//        self::bootKernel();
//
//        $product = $this->getProductRepository()->findOneBySlug('book', true);
//        self::assertInstanceOf(Product::class, $product);
//    }

    private function getProductCollectionRepository(): ProductCollectionRepository
    {
        return $this->getService(ProductCollectionRepository::class);
    }
}
