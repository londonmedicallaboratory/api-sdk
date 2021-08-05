<?php

declare(strict_types=1);

namespace LML\SDK\Model\Product;

use LML\SDK\Model\IdInterface;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Model\Money\PriceInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;

interface ProductInterface extends IdInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getShortDescription(): string;

    public function getLongDescription(): string;

    public function getPrice(): PriceInterface;

    public function getPreviewImageUrl(): ?string;

    /**
     * @return iterable<ShippingInterface>
     */
    public function getShippingTypes(): iterable;

    /**
     * @return iterable<FileInterface>
     */
    public function getFiles(): iterable;

    /**
     * @return iterable<BiomarkerInterface>
     */
    public function getBiomarkers(): iterable;

    /**
     * @return iterable<ProductFaqInterface>
     */
    public function getFaqs(): iterable;

    /**
     * @return iterable<CategoryInterface>
     */
    public function getCategories(): iterable;
}
