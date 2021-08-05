<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

use LML\SDK\Model\Money\PriceInterface;

/**
 * extends AbstractModel<array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      description: string,
 *      short_description: string,
 *      preview_image_url: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }>
 */
class Product implements ProductInterface
{
    public function __construct(
        private string $id,
        private string $name,
        private string $slug,
        private string $description,
        private string $shortDescription,
        private ?string $previewImageUrl,
        private PriceInterface $price,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPreviewImageUrl(): ?string
    {
        return $this->previewImageUrl;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getLongDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function getShippingTypes(): iterable
    {
        return [];
    }

    public function getFiles(): iterable
    {
        return [];
    }

    public function getBiomarkers(): iterable
    {
        return [];
    }

    public function getFaqs(): iterable
    {
        return [];
    }

    public function getCategories(): iterable
    {
        return [];
    }
}
