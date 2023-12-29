<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use Stringable;
use LML\SDK\Attribute\Entity;
use LML\SDK\Entity\File\File;
use LML\SDK\Entity\File\Video;
use LML\SDK\Entity\ModelInterface;
use React\Promise\PromiseInterface;
use LML\View\Lazy\LazyValueInterface;
use LML\SDK\Entity\Category\Category;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\Biomarker\Biomarker;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Repository\ProductRepository;
use function React\Promise\all;
use function React\Promise\resolve;

/**
 * @template TProductFaq of ProductFaq
 *
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      sku: string,
 *      slug?: ?string,
 *      description?: string,
 *      short_description?: ?string,
 *      is_featured?: bool,
 *      preview_image_url: ?string,
 *      price?: array{amount_minor: int, currency: string, formatted_value: string},
 *      discounted_price?: ?array{amount_minor: int, currency: string, formatted_value: string},
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
     * @param LazyValueInterface<string> $description
     * @param LazyValueInterface<string> $shortDescription
     * @param LazyValueInterface<PriceInterface> $price
     * @param LazyValueInterface<list<Shipping>> $shippingTypes
     * @param LazyValueInterface<list<File>> $files
     * @param LazyValueInterface<list<Category>> $categories
     * @param LazyValueInterface<list<Biomarker>> $biomarkers
     * @param LazyValueInterface<list<TProductFaq>> $faqs
     * @param LazyValueInterface<null|Video> $video
     * @param LazyValueInterface<?PriceInterface> $discountedPrice
     */
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $sku,
        protected string $slug,
        protected LazyValueInterface $description,
        protected LazyValueInterface $shortDescription,
        protected bool $isFeatured,
        protected ?string $previewImageUrl,
        protected LazyValueInterface $price,
        protected LazyValueInterface $biomarkers,
        protected LazyValueInterface $shippingTypes,
        protected LazyValueInterface $files,
        protected LazyValueInterface $categories,
        protected LazyValueInterface $faqs,
        protected LazyValueInterface $video,
        protected LazyValueInterface $discountedPrice,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function collect(string ...$what): PromiseInterface
    {
        /** @var list<PromiseInterface> $promises */
        $promises = [];
        foreach ($what as $name) {
            $promises[] = match ($name) {
                'biomarkers' => resolve($this->biomarkers),
                'files' => resolve($this->files),
                'shipping_types' => resolve($this->shippingTypes),
                'video' => resolve($this->video),
                default => null,
            };
        }

        return all($promises);
    }

    /**
     * @return list<Biomarker>
     */
    public function getBiomarkers(): array
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
        return $this->shortDescription->getValue();
    }

    public function getLongDescription(): string
    {
        return $this->description->getValue();
    }

    public function getPrice(): PriceInterface
    {
        return $this->price->getValue();
    }

    public function getDiscountedPrice(): ?PriceInterface
    {
        return $this->discountedPrice->getValue();
    }

    public function getDiscountedPriceOrFallback(): PriceInterface
    {
        return $this->getDiscountedPrice() ?? $this->getPrice();
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

    /**
     * @deprecated Probably, because `preview_image_url` is now usable
     *
     * TBD
     */
    public function getPrimaryImage(): ?File
    {
        $files = $this->getFiles();
        if (empty($files)) {
            return null;
        }

        foreach ($this->getFiles() as $file) {
            if ($file->isPrimary()) {
                return $file;
            }
        }

        return $this->getFiles()[0];
    }

    /**
     * @return list<TProductFaq>
     */
    public function getFaqs(): array
    {
        return $this->faqs->getValue();
    }

    /**
     * @return list<Category>
     */
    public function getCategories(): array
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
        ];
    }
}
