<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Patient\Patient;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\TestRegistrationRepository;
use LML\SDK\Entity\TestRegistration\TestRegistration;

class TestRegistrationRepositoryTest extends AbstractTest
{
    private const ID = '4991a21a-2306-4326-b279-d76715ea9fc2';


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
        $id = self::ID;

        $testRegistration = $this->getRepository()->find($id, await: true);
        self::assertInstanceOf(TestRegistration::class, $testRegistration);

        $products = $testRegistration->getProducts();
        self::assertNotEmpty($products);
    }

    public function testPatient(): void
    {
        self::bootKernel();
        $id = self::ID;

        $testRegistration = $this->getRepository()->find($id, await: true);
        self::assertInstanceOf(Patient::class, $testRegistration->getPatient());
    }

    private function getRepository(): TestRegistrationRepository
    {
        return $this->getService(TestRegistrationRepository::class);
    }
}
