<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\BiomarkerCategoryRepository;

class BiomarkerCategoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getService(BiomarkerCategoryRepository::class)->paginate(await: true);
        foreach ($pagination as $item) {
            dump($item->getLogo());
        }
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
    }
}
