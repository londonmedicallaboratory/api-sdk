<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use Pagerfanta\Pagerfanta;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\TestLocationRepository;
use LML\SDK\Entity\TestLocation\TestLocationInterface;

class TestLocationRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->paginate(await: true);

        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
        foreach ($pagination as $item) {
            self::assertInstanceOf(TestLocationInterface::class, $item);
            self::assertNotEmpty($item->getWorkingHours());
        }
    }

    public function testPagerfanta(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->pagerfanta();
        self::assertInstanceOf(Pagerfanta::class, $pagination);
        $results = $pagination->getCurrentPageResults();
        self::assertNotEmpty($results);
    }

    public function testCalendar(): void
    {
        $id = 'fbffe852-6baa-4f52-9df9-6595b60eddf0';
        self::bootKernel();
        $repo = $this->getRepository();
        $calendar = $repo->getMonthlyCalender($id, new DateTime());
        dd($calendar);
    }

    public function testSlots(): void
    {
        $id = 'fbffe852-6baa-4f52-9df9-6595b60eddf0';
        self::bootKernel();
        $repo = $this->getRepository();
        $slots = $repo->getSlots($id, new DateTime());
        foreach ($slots as $slot) {
            dd($slot->toArray());
        }

        dd($slots);
    }

    private function getRepository(): TestLocationRepository
    {
        return $this->getService(TestLocationRepository::class);
    }
}
