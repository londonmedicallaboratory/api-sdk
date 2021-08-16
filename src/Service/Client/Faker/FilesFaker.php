<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Model\File\FileInterface;

/**
 * @psalm-import-type S from FileInterface
 * @implements FakerInterface<S>
 *
 * @see FileInterface
 */
class FilesFaker implements FakerInterface
{
    public function getPaginatedData()
    {
        yield '/product/1/files' => [
            'current_page'     => 1,
            'nr_of_results'    => 0,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items' => [
            ],
        ];

        yield '/product/2/files' => [
            'current_page'     => 1,
            'nr_of_results'    => 0,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items' => [
            ],
        ];
    }
}
