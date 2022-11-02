<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\FAQ\Category;
use LML\SDK\Repository\FAQ\CategoryRepository;

class FAQCategoryRepositoryTest extends AbstractTest
{
    public function testFetch(): void
    {
        self::bootKernel();
        $repo = $this->getRepository();
        $results = $repo->findAll(await: true);
        self::assertNotEmpty($results);
        foreach ($results as $category) {
            self::assertInstanceOf(Category::class, $category);
        }
    }

    private function getRepository(): CategoryRepository
    {
        return $this->getService(CategoryRepository::class);
    }
}
