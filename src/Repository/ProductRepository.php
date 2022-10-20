<?php

declare(strict_types=1);

namespace LML\SDK\Repository;

use LML\SDK\Lazy\LazyPromise;
use LML\SDK\Entity\File\Video;
use LML\SDK\Entity\Money\Price;
use LML\SDK\Entity\Product\Product;
use React\Promise\PromiseInterface;
use LML\SDK\Entity\Shipping\Shipping;
use LML\SDK\Entity\File\FileInterface;
use LML\SDK\Entity\Product\ProductFaq;
use LML\SDK\Service\API\AbstractRepository;
use LML\SDK\Entity\Product\ProductInterface;
use LML\SDK\Exception\DataNotFoundException;
use LML\SDK\Entity\Category\CategoryInterface;
use LML\SDK\Entity\Biomarker\BiomarkerInterface;
use function sprintf;

/**
 * @psalm-import-type S from ProductInterface
 *
 * @extends AbstractRepository<S, Product, array{
 *     slug?: string,
 *     search?: string,
 *     sku?: string,
 *     category_slug?: string,
 *     collection_slug?: string,
 *     product_categories?: list<string>,
 *     featured?: bool,
 * }>
 */
class ProductRepository extends AbstractRepository
{
    protected function one($entity, $options, $optimizer): Product
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
            sku: $entity['sku'],
            slug: $entity['slug'] ?? throw new DataNotFoundException(),
            description: $entity['description'] ?? throw new DataNotFoundException(),
            shortDescription: $entity['short_description'] ?? throw new DataNotFoundException(),
            previewImageUrl: $entity['preview_image_url'],
            isFeatured: $entity['is_featured'] ?? false,
            price: $price,
            biomarkers: new LazyPromise($this->getBiomarkers($id)),
            shippingTypes: new LazyPromise($this->getShippingTypes($id)),
            files: new LazyPromise($this->getFiles($id)),
            categories: new LazyPromise($this->getCategories($id)),
            faqs: new LazyPromise($this->getFaqs($id)),
            video: new LazyPromise($this->getVideo($id)),
        );
    }

    /**
     * @return PromiseInterface<list<CategoryInterface>>
     */
    private function getCategories(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/categories', $id);

        return $this->get(ProductCategoryRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<?Video>
     */
    private function getVideo(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/video', $id);

        return $this->get(VideoRepository::class)->findOneBy(url: $url);
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
     * @return PromiseInterface<list<Shipping>>
     */
    private function getShippingTypes(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/shipping', $id);

        return $this->get(ShippingRepository::class)->findBy(url: $url);
    }

    /**
     * @return PromiseInterface<list<ProductFaq>>
     */
    private function getFaqs(string $id): PromiseInterface
    {
        $url = sprintf('/product/%s/faqs', $id);

        return $this->get(ProductFaqRepository::class)->findBy(url: $url);
    }
}
