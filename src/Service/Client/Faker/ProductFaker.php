<?php

declare(strict_types=1);

namespace LML\SDK\Service\Client\Faker;

use LML\SDK\Model\Product\ProductInterface;

/**
 * @psalm-import-type S from ProductInterface
 * @implements FakerInterface<S>
 *
 * @see ProductInterface
 */
class ProductFaker implements FakerInterface
{
    public const PRODUCT_1 = [
        'id'                => '1',
        'name'              => 'Thyroid Function Baseline Profile',
        'slug'              => 'thyroid-function-baseline-profile',
        'short_description' => 'Check the function of your thyroid gland. Thyroid disorders are common but often remain undiagnosed.',
        'description'       => 'This profile is to check the function of your thyroid gland. It checks the level of Thyroid Stimulating Hormone (TSH)',
        'preview_image_url' => 'https://s3.eu-west-2.amazonaws.com/media.londonmedicallaboratory.co.uk/prod/files/60a57552568bf187116724.jpg',
        'price'             => [
            'amount_minor'    => 3900,
            'currency'        => 'GBP',
            'formatted_value' => '£39.00',
        ],
    ];

    public const PRODUCT_2 = [
        'id'                => '2',
        'name'              => 'Enhanced Biochemistry Profile',
        'slug'              => 'enhanced-biochemistry-profile',
        'short_description' => 'A comprehensive check of your liver & kidney function, bone health, iron levels and a full cholesterol profile.',
        'description'       => 'This profile is a comprehensive check of your liver & kidney function, your bone health, iron levels and your full cholesterol profile.',
        'preview_image_url' => null,
        'price'             => [
            'amount_minor'    => 3900,
            'currency'        => 'GBP',
            'formatted_value' => '£39.00',
        ],
    ];

    public function getPaginatedData()
    {
        yield '/product' => [
            'current_page'     => 1,
            'nr_of_results'    => 1,
            'nr_of_pages'      => 1,
            'results_per_page' => 10,
            'next_page'        => null,
            'items'            => [
                self::PRODUCT_1,
                self::PRODUCT_2,
            ],
        ];
        yield '/product/1' => self::PRODUCT_1;
    }
}
