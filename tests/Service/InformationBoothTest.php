<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Service;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Service\InformationBooth;

class InformationBoothTest extends AbstractTest
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        $booth = $this->getService(InformationBooth::class);

        $info = $booth->getWebsiteInfo();
        self::assertNotNull($info);
    }
}
