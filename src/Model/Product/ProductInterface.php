<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

use LML\SDK\Model\ModelInterface;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;

/**
 * @psalm-type S=array{
 *      id: string,
 *      name: string,
 *      slug: string,
 *      description: string,
 *      short_description: string,
 *      preview_image_url: ?string,
 *      price: array{amount_minor: int, currency: string, formatted_value: string},
 * }
 *
 * @extends ModelInterface<S>
 */
interface ProductInterface extends ModelInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getShortDescription(): string;

    public function getLongDescription(): string;

    public function getPrice(): PriceInterface;

    public function getPreviewImageUrl(): ?string;

    /**
     * @return list<ShippingInterface>
     */
    public function getShippingTypes();

    /**
     * @return list<FileInterface>
     */
    public function getFiles();

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
