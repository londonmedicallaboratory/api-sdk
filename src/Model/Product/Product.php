<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;

class Product implements ProductInterface
{
    /**
     * @param LazyValue<list<BiomarkerInterface>> $biomarkers
     * @param LazyValue<list<ShippingInterface>> $shippingTypes
     * @param LazyValue<list<FileInterface>> $files
     * @param LazyValue<list<CategoryInterface>> $categories
     */
    public function __construct(
        private string $id,
        private string $name,
        private string $slug,
        private string $description,
        private string $shortDescription,
        private ?string $previewImageUrl,
        private PriceInterface $price,
        private LazyValue $biomarkers,
        private LazyValue $shippingTypes,
        private LazyValue $files,
        private LazyValue $categories,
    )
    {
    }

    public function getBiomarkers()
    {
        return $this->biomarkers->getValue();
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

    public function getShippingTypes()
    {
        return $this->shippingTypes->getValue();
    }

    public function getFiles()
    {
        return $this->files->getValue();
    }

    public function getFaqs()
    {
        return [];
    }

    public function getCategories()
    {
        return $this->categories->getValue();
    }

    public function toArray()
    {
        $price = $this->getPrice();

        return [
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'slug'              => $this->getSlug(),
            'description'       => $this->getLongDescription(),
            'short_description' => $this->getShortDescription(),
            'preview_image_url' => $this->getPreviewImageUrl(),
            'price'             => [
                'amount_minor'    => $price->getAmount(),
                'currency'        => $price->getCurrency(),
                'formatted_value' => $price->getFormattedValue(),
            ],
        ];
    }
}