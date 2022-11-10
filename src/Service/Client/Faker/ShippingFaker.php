<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use Generator;
use LML\SDK\Entity\Shipping\Shipping;

/**
 * @psalm-import-type S from Shipping
 *
 * @implements FakerInterface<S>
 */
class ShippingFaker implements FakerInterface
{
    public const SHIPPING_1 = [
        'id' => '1',
        'name' => 'Free Shipping',
        'type' => '1',
        'description' => 'Free Shipping description',
        'price' => [
            'amount_minor' => 100,
            'currency' => 'USD',
            'formatted_value' => '$1',
        ],
    ];

    public const SHIPPING_2 = [
        'id' => '2',
        'name' => 'UPS',
        'type' => '2',
        'description' => 'UPS description',
        'price' => [
            'amount_minor' => 100,
            'currency' => 'USD',
            'formatted_value' => '$1',
        ],
    ];

    public function getPaginatedData(): Generator
    {
        yield '/product/1/shipping' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::SHIPPING_1,
            ],
        ];

        yield '/product/2/shipping' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::SHIPPING_2,
            ],
        ];

        yield '/shipping' => [
            'current_page' => 1,
            'nr_of_results' => 1,
            'nr_of_pages' => 1,
            'results_per_page' => 10,
            'next_page' => null,
            'items' => [
                self::SHIPPING_1,
                self::SHIPPING_2,
            ],
        ];
    }
}
