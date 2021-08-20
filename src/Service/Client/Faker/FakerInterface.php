<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use Generator;

/**
 * @template T
 */
interface FakerInterface
{
    /**
     * @return Generator<string, array{current_page: int, nr_of_results: int, nr_of_pages: int, results_per_page: int, next_page: ?int, items: list<T>}>
     */
    public function getPaginatedData();
}
