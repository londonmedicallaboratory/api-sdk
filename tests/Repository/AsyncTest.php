<?php

declare(strict_types=1);

namespace LML\SDK\Tests\Repository;

use LML\SDK\Tests\AbstractTest;
use LML\SDK\Entity\Product\Product;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Repository\ProductRepository;
use Symfony\Component\Stopwatch\Stopwatch;
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
        self::assertInstanceOf(Product::class, $book);

        $event = $stopwatch->stop('init');
        $time = $event->getDuration();

        $testing = $book->getCategories();
        self::assertGreaterThanOrEqual(1, count($testing));
        foreach ($testing as $item) {
            self::assertInstanceOf(Category::class, $item);
        }

        $biomarkers = $book->getBiomarkers();
        self::assertNotEmpty($biomarkers);
        foreach ($biomarkers as $biomarker) {
            self::assertInstanceOf(Biomarker::class, $biomarker);
        }

        $files = $book->getFiles();
        self::assertIsArray($files);

        self::assertLessThan(5000, $time);
    }
}
