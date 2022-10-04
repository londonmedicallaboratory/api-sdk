<?php

declare(strict_types=1);

namespace LML\SDK\Entity\Product;

use Stringable;
use LML\SDK\Entity\ModelInterface;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Entity\SluggableInterface;
use LML\SDK\Entity\Money\PriceInterface;
use LML\SDK\Entity\Shipping\ShippingInterface;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      sku: string,
 *      slug?: ?string,
 *      description: string,
 *      short_description?: ?string,
 *      is_featured?: bool,
 *      preview_image_url: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @extends ModelInterface<S>
 */
interface ProductInterface extends ModelInterface, SluggableInterface, Stringable
{
    public function getName(): string;

    public function getSku(): string;

    public function getSlug(): string;

    public function getShortDescription(): string;

    public function getLongDescription(): string;

    public function getPrice(): PriceInterface;

    public function getPreviewImageUrl(): ?string;

    public function isFeatured(): bool;

    /**
     * @return list<ShippingInterface>
     */
    public function getShippingTypes(): array;

    /**
     * @return list<FileInterface>
     */
    public function getFiles(): array;

    /**
     * @return list<BiomarkerInterface>
     */
    public function getBiomarkers();

    /**
     * @return list<ProductFaqInterface>
     */
    public function getFaqs();

    /**
     * @return list<CategoryInterface>
     */
    public function getCategories();
}
