<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LogicException;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\BiomarkerCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BiomarkerCategoryRepositoryTest extends KernelTestCase
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }

    private function getRepository(): BiomarkerCategoryRepository
    {
        $repo = self::$kernel->getContainer()->get(BiomarkerCategoryRepository::class);

        return $repo instanceof BiomarkerCategoryRepository ? $repo : throw new LogicException();
    }
}
