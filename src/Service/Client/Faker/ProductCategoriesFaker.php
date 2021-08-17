<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Model\Category\CategoryInterface;

/**
 * @psalm-import-type S from CategoryInterface
 * @implements FakerInterface<S>
 *
 * @see CategoryInterface
 */
class ProductCategoriesFaker implements FakerInterface
{
    public const CATEGORY_1 = [
        'id'          => '1',
        'name'        => 'Health check kits',
        'slug'        => 'health-check',
        'description' => 'Health Check',
    ];

    public const CATEGORY_2 = [
        'id'          => '2',
        'name'        => 'Fertility',
        'slug'        => 'fertility',
        'description' => 'Fertility description',
    ];

    public const CATEGORY_3 = [
        'id'          => '3',
        'name'        => 'Sexual Health',
        'slug'        => 'sexual-health',
        'description' => 'Sexual Health description',
    ];

    public function getPaginatedData()
    {
        yield '/product_categories' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items'            => [
                self::CATEGORY_1,
                self::CATEGORY_2,
                self::CATEGORY_3,
            ],
        ];
    }
}
