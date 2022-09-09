<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\TestRegistrationRepository;
use LML\SDK\Entity\TestRegistration\TestRegistration;

class TestRegistrationRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    public function testProductFiles(): void
    {
        self::bootKernel();
        $id = '4bf50271-3d8f-4319-83c7-df55d5507c73';

        $testRegistration = $this->getRepository()->find($id, await: true);
        self::assertInstanceOf(TestRegistration::class, $testRegistration);

        $products = $testRegistration->getProducts();
        self::assertNotEmpty($products);
    }

    private function getRepository(): TestRegistrationRepository
    {
        return $this->getService(TestRegistrationRepository::class);
    }
}
