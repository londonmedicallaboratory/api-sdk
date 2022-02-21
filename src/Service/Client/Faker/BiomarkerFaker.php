<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Entity\Biomarker\BiomarkerInterface;

/**
 * @psalm-import-type S from BiomarkerInterface
 * @implements FakerInterface<S>
 *
 * @see BiomarkerInterface
 */
class BiomarkerFaker implements FakerInterface
{
    public const BIOMARKER_1 = [
        'id'          => '1',
        'name'        => 'Total Cholesterol ',
        'slug'        => '1',
        'code'        => 'Total Cholesterol ',
        'description' => 'Total Cholesterol ',
    ];

    public const BIOMARKER_2 = [
        'id'          => '2',
        'name'        => 'High Density Lipoprotein',
        'slug'        => '2',
        'code'        => 'High Density Lipoprotein',
        'description' => 'High Density Lipoprotein',
    ];

    public const BIOMARKER_3 = [
        'id'          => '3',
        'name'        => 'Low Density Lipoprotein',
        'slug'        => '3',
        'code'        => 'Low Density Lipoprotein',
        'description' => 'Low Density Lipoprotein',
    ];

    public function getPaginatedData()
    {
        yield '/product/1/biomarkers' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items'            => [
                self::BIOMARKER_1,
                self::BIOMARKER_2,
                self::BIOMARKER_3,
            ],
        ];
        yield '/product/2/biomarkers' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items' => [
                self::BIOMARKER_2,
            ],
        ];
    }
}
