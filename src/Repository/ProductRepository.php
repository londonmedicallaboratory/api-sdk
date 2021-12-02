<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Model\Money\Price;
use LML\SDK\Model\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\Model\File\FileInterface;
use LML\SDK\Model\Product\ProductInterface;
use LML\SDK\Model\Category\CategoryInterface;
use LML\SDK\Model\Shipping\ShippingInterface;
use LML\SDK\Service\Model\AbstractRepository;
use LML\SDK\Model\Biomarker\BiomarkerInterface;
use function sprintf;

/**
 * @psalm-import-type S from ProductInterface
 * @extends AbstractRepository<S, Product, array>
 *
 * @see Product
 * @see ProductInterface
 */
class ProductRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer)
    {
        $priceData = $entity['price'];

        $price = new Price(
            amount        : $priceData['amount_minor'],
            currency      : $priceData['currency'],
            formattedValue: $priceData['formatted_value'],
        );

        $id = $entity['id'];

        return new Product(
            id              : $id,
            name            : $entity['name'],
            slug            : $entity['slug'],
            description     : $entity['description'],
            shortDescription: $entity['short_description'],
            previewImageUrl : $entity['preview_image_url'],
            testToRelease   : $entity['test_to_release'],
            price           : $price,
            biomarkers      : new LazyPromise($this->getBiomarkers($id)),
            shippingTypes   : new LazyPromise($this->getShippingTypes($id)),
            files           : new LazyPromise($this->getFiles($id)),
            categories      : new LazyPromise($this->getCategories($id)),
        );
    }

    protected function getBaseUrl(): string
    {
        return '/product';
    }

    /**
     * @return PromiseInterface<list<CategoryInterface>>
     */
    private function getCategories(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/categories', $id);

        return $this->get(BiomarkerCategoryRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<FileInterface>>
     */
    private function getFiles(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/files', $id);

        return $this->get(FileRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<BiomarkerInterface>>
     */
    private function getBiomarkers(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/biomarkers', $id);

        return $this->get(BiomarkerRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<ShippingInterface>>
     */
    private function getShippingTypes(string $id)
    {
        $url = sprintf('/product/%s/shipping', $id);

        return $this->get(ShippingRepository::class)->findBy(url: $url);
    }
}
