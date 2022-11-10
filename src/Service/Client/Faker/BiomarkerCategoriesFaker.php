<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Entity\Category\Category;

/**
 * @psalm-import-type S from Category
 *
 * @implements FakerInterface<S>
 */
class BiomarkerCategoriesFaker implements FakerInterface
{
    public const CATEGORY_1 = [
        'id' => '1',
        'name' => 'Cholesterol',
        'slug' => 'cholesterol',
        'description' => 'Cholesterol description',
        'logo' => null,
    ];

    public const CATEGORY_2 = [
        'id' => '2',
        'name' => 'Diabetes',
        'slug' => 'diabetes',
        'description' => 'Diabetes description',
        'logo' => null,
    ];

    public const CATEGORY_3 = [
        'id' => '3',
        'name' => 'Triglyceride',
        'slug' => 'triglyceride',
        'description' => 'Triglyceride description',
        'logo' => null,
    ];

    public function getPaginatedData()
    {
        yield '/product/1/categories' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_1,
            ],
        ];

        yield '/product/2/categories' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_1,
            ],
        ];

        yield '/biomarker/1/category' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_1,
            ],
        ];

        yield '/biomarker/2/category' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_1,
                self::CATEGORY_2,
            ],
        ];

        yield '/biomarker/3/category' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_3,
            ],
        ];

        yield '/biomarker_categories' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::CATEGORY_1,
                self::CATEGORY_2,
                self::CATEGORY_3,
            ],
        ];
    }
}
