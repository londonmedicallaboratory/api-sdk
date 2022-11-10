<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\File\File;
use LML\SDK\Entity\File\Video;
use LML\SDK\Entity\ModelInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ProductRepository;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      sku: string,
 *      slug?: ?string,
 *      description?: string,
 *      short_description?: ?string,
 *      is_featured?: bool,
 *      preview_image_url: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @implements ModelInterface<S>
 */
#[Entity(repositoryClass: ProductRepository::class, baseUrl: 'product')]
class Product implements ModelInterface, SluggableInterface, Stringable
{
    /**
     * @see ProductRepository::one()
     *
     * @param LazyValueInterface<list<Shipping>> $shippingTypes
     * @param LazyValueInterface<list<File>> $files
     * @param LazyValueInterface<list<Category>> $categories
     * @param LazyValueInterface<list<Biomarker>> $biomarkers
     * @param LazyValueInterface<list<ProductFaq>> $faqs
     * @param LazyValueInterface<null|Video> $video
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $sku,
        protected string $slug,
        protected string $description,
        protected string $shortDescription,
        protected bool $isFeatured,
        protected ?string $previewImageUrl,
        protected PriceInterface $price,
        protected LazyValueInterface $biomarkers,
        protected LazyValueInterface $shippingTypes,
        protected LazyValueInterface $files,
        protected LazyValueInterface $categories,
        protected LazyValueInterface $faqs,
        protected LazyValueInterface $video,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return list<Biomarker>
     */
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

    public function isFeatured(): bool
    {
        return $this->isFeatured;
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

    /**
     * @return list<Shipping>
     */
    public function getShippingTypes(): array
    {
        return $this->shippingTypes->getValue();
    }

    /**
     * @return list<File>
     */
    public function getFiles(): array
    {
        return $this->files->getValue();
    }

    public function getFaqs(): array
    {
        return $this->faqs->getValue();
    }

    /**
     * @return list<Category>
     */
    public function getCategories()
    {
        return $this->categories->getValue();
    }

    public function getVideo(): ?Video
    {
        return $this->video->getValue();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'sku' => $this->getSku(),
            'preview_image_url' => $this->getPreviewImageUrl(),
            'price' => $this->getPrice()->toArray(),
        ];
    }
}
