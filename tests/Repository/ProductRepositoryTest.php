<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LogicException;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    private function getRepository(): ProductRepository
    {
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);

        return $repo instanceof ProductRepository ? $repo : throw new LogicException();
    }
}
