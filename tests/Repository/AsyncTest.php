<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Model\Product\Product;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Repository\ProductRepository;
use Symfony\Component\Stopwatch\Stopwatch;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function count;

class AsyncTest extends KernelTestCase
{
    public function testFindOneBy(): void
    {
        self::bootKernel();
        /** @var ProductRepository $repo */
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);

        $stopwatch = new Stopwatch();
        $stopwatch->start('init');

        /** @var Product $book */
        $book = $repo->findLazy(['slug' => 'book'])->getValue();
        self::assertInstanceOf(ProductInterface::class, $book);

        $event = $stopwatch->stop('init');
        $time = $event->getDuration();

        $testing = $book->getCategories();
        self::assertGreaterThanOrEqual(1, count($testing));
        foreach ($testing as $item) {
            self::assertInstanceOf(CategoryInterface::class, $item);
        }

        $biomarkers = $book->getBiomarkers();
        self::assertGreaterThanOrEqual(2, count($biomarkers));
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
        /** @var ProductRepository $repo */
        $repo = self::$kernel->getContainer()->get(ProductRepository::class);

        $lazyValue = $repo->findLazy(['slug' => 'book']);
        /** @var Product $book */
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

//        dump($book->getCategories());
    }
}
