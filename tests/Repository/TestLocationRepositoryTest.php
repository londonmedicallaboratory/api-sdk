<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use DateTime;
use Pagerfanta\Pagerfanta;
use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Brand\Brand;
use LML\SDK\Entity\PaginatedResults;
use LML\SDK\Repository\BrandRepository;
use LML\SDK\Entity\Brand\Calender\Slot;

class TestLocationRepositoryTest extends AbstractTest
{
    public function testPagination(): void
    {
        self::bootKernel();

        $pagination = $this->getRepository()->paginate(await: true);

        self::assertInstanceOf(PaginatedResults::class, $pagination);
        self::assertNotEmpty($pagination->getItems());
        foreach ($pagination as $item) {
            self::assertInstanceOf(Brand::class, $item);
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
        self::assertNotEmpty($calendar);
    }

    public function testSlots(): void
    {
        $id = 'fbffe852-6baa-4f52-9df9-6595b60eddf0';
        self::bootKernel();
        $repo = $this->getRepository();
        $slots = $repo->getSlots($id, new DateTime());
        self::assertNotEmpty($slots);
        foreach ($slots as $slot) {
            self::assertInstanceOf(Slot::class, $slot);
        }

    }

    private function getRepository(): BrandRepository
    {
        return $this->getService(BrandRepository::class);
    }
}
