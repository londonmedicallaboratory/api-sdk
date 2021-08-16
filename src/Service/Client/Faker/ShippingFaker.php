<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Model\Shipping\ShippingInterface;

/**
 * @psalm-import-type S from ShippingInterface
 * @implements FakerInterface<S>
 *
 * @see ShippingInterface
 */
class ShippingFaker implements FakerInterface
{
    public const SHIPPING_1 = [
        'id'          => '1',
        'name'        => '1',
        'type'        => '1',
        'description' => '1',
    ];

    public const SHIPPING_2 = [
        'id'          => '2',
        'name'        => '2',
        'type'        => '2',
        'description' => '2',
    ];

    public function getPaginatedData()
    {
        yield '/product/1/shipping' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items' => [
                self::SHIPPING_1,
            ],
        ];

        yield '/product/2/shipping' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items'            => [
                self::SHIPPING_2,
            ],
        ];
    }
}
