<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Entity\Biomarker\Biomarker;

/**
 * @psalm-import-type S from Biomarker
 *
 * @implements FakerInterface<S>
 */
class BiomarkerFaker implements FakerInterface
{
    public const BIOMARKER_1 = [
        'id' => '1',
        'name' => 'Total Cholesterol ',
        'slug' => '1',
        'code' => 'Total Cholesterol ',
        'description' => 'Total Cholesterol ',
        'category_id' => '1',
    ];

    public const BIOMARKER_2 = [
        'id' => '2',
        'name' => 'High Density Lipoprotein',
        'slug' => '2',
        'code' => 'High Density Lipoprotein',
        'description' => 'High Density Lipoprotein',
        'category_id' => '2',
    ];

    public const BIOMARKER_3 = [
        'id' => '3',
        'name' => 'Low Density Lipoprotein',
        'slug' => '3',
        'code' => 'Low Density Lipoprotein',
        'description' => 'Low Density Lipoprotein',
        'category_id' => '3',
    ];

    public function getPaginatedData()
    {
        yield '/product/1/biomarkers' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::BIOMARKER_1,
                self::BIOMARKER_2,
                self::BIOMARKER_3,
            ],
        ];
        yield '/product/2/biomarkers' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::BIOMARKER_2,
            ],
        ];
    }
}
