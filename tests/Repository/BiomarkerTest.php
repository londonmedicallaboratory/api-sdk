<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\BiomarkerRepository;
use LML\SDK\DataCollector\ClientDataCollector;
use function Clue\React\Block\await;

class BiomarkerTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getService(BiomarkerRepository::class)->paginate(await: true);
        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
//        dump($pagination->getItems());

        $profiler = $this->getService(ClientDataCollector::class);
        dump($profiler->getRequests());
    }

    public function testIdentityMap(): void
    {
        self::bootKernel();

        $repository = $this->getService(BiomarkerRepository::class);
        $bio1 = $repository->find('11111111-1111-1111-1111-111111111111');
        $bio2 = $repository->find('11111111-1111-1111-1111-111111111111');

        $bio1 = await($bio1);
        $bio2 = await($bio2);

        self::assertSame($bio1, $bio2);
    }
}
