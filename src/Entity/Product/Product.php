<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use LML\SDK\Attribute\Entity;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ProductRepository;
use LML\SDK\Entity\Shipping\ShippingInterface;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;

#[Entity(repositoryClass: ProductRepository::class, baseUrl: 'product')]
class Product implements ProductInterface
{
    /**
     * @see ProductRepository::one()
     *
     * @param LazyValueInterface<list<ShippingInterface>> $shippingTypes
     * @param LazyValueInterface<list<FileInterface>> $files
     * @param LazyValueInterface<list<CategoryInterface>> $categories
     * @param LazyValueInterface<list<BiomarkerInterface>> $biomarkers
     * @param LazyValueInterface<list<ProductFaq>> $faqs
     */
    public function __construct(
        protected string             $id,
        protected string             $name,
        protected string             $sku,
        protected string             $slug,
        protected string             $description,
        protected string             $shortDescription,
        protected ?string            $previewImageUrl,
        protected bool               $testToRelease,
        protected PriceInterface     $price,
        protected LazyValueInterface $biomarkers,
        protected LazyValueInterface $shippingTypes,
        protected LazyValueInterface $files,
        protected LazyValueInterface $categories,
        protected LazyValueInterface $faqs,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
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

    public function getSku(): string
    {
        return $this->sku;
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

    public function getFaqs(): array
    {
        return $this->faqs->getValue();
    }

    public function getCategories()
    {
        return $this->categories->getValue();
    }

    public function isTestToRelease(): bool
    {
        return $this->testToRelease;
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'sku'               => $this->getSku(),
            'slug'              => $this->getSlug(),
            'description'       => $this->getLongDescription(),
            'short_description' => $this->getShortDescription(),
            'preview_image_url' => $this->getPreviewImageUrl(),
            'test_to_release'   => $this->isTestToRelease(),
            'price'             => $this->getPrice()->toArray(),
        ];
    }
}
