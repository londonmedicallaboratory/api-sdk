<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\TestLocationRepository;
use LML\SDK\Entity\TestLocation\TestLocationInterface;

class TestLocationRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getProductRepository()->paginate(await: true);

        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
        foreach ($pagination as $item) {
            self::assertInstanceOf(TestLocationInterface::class, $item);
            self::assertNotEmpty($item->getWorkingHours());
        }
    }

    private function getProductRepository(): TestLocationRepository
    {
        return $this->getService(TestLocationRepository::class);
    }
}
