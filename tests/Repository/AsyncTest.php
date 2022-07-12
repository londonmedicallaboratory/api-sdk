<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Repository\ProductRepository;
use Symfony\Component\Stopwatch\Stopwatch;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;
use function count;

class AsyncTest extends AbstractTest
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        $repo = $this->getService(ProductRepository::class);

        $stopwatch = new Stopwatch();
        $stopwatch->start('init');

        $book = $repo->findOneBySlug('book', await: true);
        self::assertInstanceOf(ProductInterface::class, $book);

        $event = $stopwatch->stop('init');
        $time = $event->getDuration();

        $testing = $book->getCategories();
        self::assertGreaterThanOrEqual(1, count($testing));
        foreach ($testing as $item) {
            self::assertInstanceOf(CategoryInterface::class, $item);
        }

        $biomarkers = $book->getBiomarkers();
        self::assertNotEmpty($biomarkers);
        foreach ($biomarkers as $biomarker) {
            self::assertInstanceOf(BiomarkerInterface::class, $biomarker);
        }

        $files = $book->getFiles();
        self::assertIsArray($files);

        self::assertLessThan(5000, $time);
    }

    public function testFindOneAsync(): void
    {
        self::bootKernel();
        $repo = $this->getService(ProductRepository::class);

        $lazyValue = $repo->findLazy(['slug' => 'book']);
        $book = $lazyValue->getValue();

        self::assertInstanceOf(ProductInterface::class, $book);

        foreach ($book->getCategories() as $category) {
            self::assertInstanceOf(CategoryInterface::class, $category);
        }
        foreach ($book->getBiomarkers() as $biomarker) {
            self::assertInstanceOf(BiomarkerInterface::class, $biomarker);
        }
        foreach ($book->getFiles() as $biomarker) {
            self::assertInstanceOf(FileInterface::class, $biomarker);
        }
        foreach ($book->getShippingTypes() as $biomarker) {
            self::assertInstanceOf(ShippingInterface::class, $biomarker);
        }
    }
}
