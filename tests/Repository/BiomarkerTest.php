<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\BiomarkerRepository;
use LML\SDK\DataCollector\ClientDataCollector;

class BiomarkerTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getService(BiomarkerRepository::class)->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());

        $profiler = $this->getService(ClientDataCollector::class);
        dump($profiler->getRequests());
    }
}
