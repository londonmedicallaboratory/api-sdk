<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\View\Lazy\LazyValue;
use LML\SDK\Model\Money\Price;
use LML\SDK\Model\Product\Product;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Model\Biomarker\BiomarkerInterface;
use LML\SDK\ViewFactory\AbstractViewRepository;
use function sprintf;

/**
 * @psalm-import-type S from ProductInterface
 * @extends AbstractViewRepository<S, Product, array>
 *
 * @see Product
 * @see ProductInterface
 */
class ProductRepository extends AbstractViewRepository
{
    protected function one($entity, $options, LazyValue $optimizer)
    {
        $priceData = $entity['price'];

        $price = new Price(
            amount: $priceData['amount_minor'],
            currency: $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        $id = $entity['id'];

        return new Product(
            id: $id,
            name: $entity['name'],
            slug: $entity['slug'],
            description: $entity['description'],
            shortDescription: $entity['short_description'],
            previewImageUrl: $entity['preview_image_url'],
            price: $price,
            biomarkers: new LazyValue(fn() => $this->getBiomarkers($id)),
            shippingTypes: new LazyValue(fn() => $this->getShippingTypes($id)),
            files: new LazyValue(fn() => $this->getFiles($id)),
            categories: new LazyValue(fn() => $this->getCategories($id)),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product';
    }

    /**
     * @return list<FileInterface>
     */
    private function getFiles(string $id)
    {
        $url = sprintf('/product/%s/files', $id);

        return $this->get(FileRepository::class)->findFromUrl($url);
    }

    /**
     * @return list<CategoryInterface>
     */
    private function getCategories(string $id)
    {
        $url = sprintf('/product/%s/categories', $id);

        return $this->get(CategoryRepository::class)->findFromUrl($url);
    }

    /**
     * @return list<BiomarkerInterface>
     */
    private function getBiomarkers(string $id)
    {
        $url = sprintf('/product/%s/biomarkers', $id);

        return $this->get(BiomarkerRepository::class)->findFromUrl($url);
    }

    /**
     * @return list<ShippingInterface>
     */
    private function getShippingTypes(string $id)
    {
        $url = sprintf('/product/%s/shipping', $id);

        return $this->get(ShippingRepository::class)->findFromUrl($url);
    }
}
