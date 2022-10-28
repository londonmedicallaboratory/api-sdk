<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Entity\FAQ\FAQ;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Repository\FAQ\FAQRepository;

class FAQRepositoryTest extends AbstractTest
{
    public function testInvalidCode(): void
    {
        self::bootKernel();
        $repo = $this->getRepository();
        $results = $repo->findAll(await: true);
        self::assertNotEmpty($results);
        foreach ($results as $faq) {
            self::assertInstanceOf(FAQ::class, $faq);
        }
    }

    private function getRepository(): FAQRepository
    {
        return $this->getService(FAQRepository::class);
    }
}
