<?php

declare(strict_types=1);

namespace LML\SDK\Factory;

use LML\SDK\Model\Money\Price;
use LML\SDK\Model\Product\Product;
use LML\SDK\Model\Product\ProductInterface;

/**
 * @implements ModelMapperInterface<ProductInterface, array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      description: string,
 *      short_description: string,
 *      preview_image_url: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }>
 *
 * @see ProductInterface
 */
class ProductMapper implements ModelMapperInterface
{
    /**
     * @param ProductInterface $model
     */
    public function toArray($model)
    {
        $price = $model->getPrice();

        return [
            'id'                => $model->getId(),
            'name'              => $model->getName(),
            'slug'              => $model->getSlug(),
            'description'       => $model->getLongDescription(),
            'short_description' => $model->getShortDescription(),
            'preview_image_url' => $model->getPreviewImageUrl(),
            'price'             => [
                'amount_minor'    => $price->getAmount(),
                'currency'        => $price->getCurrency(),
                'formatted_value' => $price->getFormattedValue(),
            ],
        ];
    }

    public function fromArray($input): Product
    {
        $priceData = $input['price'];

        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        return new Product(
            id: $input['id'],
            name: $input['name'],
            slug: $input['slug'],
            description: $input['description'],
            shortDescription: $input['short_description'],
            previewImageUrl: $input['preview_image_url'],
            price: $price,
        );
    }
}
