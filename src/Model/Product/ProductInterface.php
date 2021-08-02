<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Model\IdInterface;
use App\Model\File\FileInterface;
use App\Model\Shipping\ShippingInterface;
use App\Model\Category\CategoryInterface;
use App\Model\Biomarker\BiomarkerInterface;

interface ProductInterface extends IdInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function getShortDescription(): string;

    public function getLongDescription(): string;

    public function getPrice(): string;

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
