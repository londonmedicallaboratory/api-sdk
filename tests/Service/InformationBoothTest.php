<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Service;

use LML\SDK\Service\InformationBooth;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InformationBoothTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var InformationBooth $booth */
        $booth = self::$kernel->getContainer()->get(InformationBooth::class);

        $info = $booth->getWebsiteInfo();
        self::assertNotNull($info);
    }
}
